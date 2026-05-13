<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceRequest;
use App\Models\JobOrder;
use App\Models\User;
use App\Notifications\CashAdvanceReviewed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CashAdvanceRequestController extends Controller
{
    public function index()
    {
        $userId = request('user_id');

        $baseQuery = CashAdvanceRequest::query()
            ->with(['user', 'items'])
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });

        $pendingJoRequests = $this->applyJoCashAdvanceFilter((clone $baseQuery)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'Pending');
            }))
            ->latest()
            ->paginate(10, ['*'], 'pending_jo_page')
            ->withQueryString();

        $pendingPersonalRequests = $this->applyPersonalCashAdvanceFilter((clone $baseQuery)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'Pending');
            }))
            ->latest()
            ->paginate(10, ['*'], 'pending_personal_page')
            ->withQueryString();

        $approvedJoRequests = $this->applyJoCashAdvanceFilter((clone $baseQuery)->where('status', 'Approved'))
            ->latest()
            ->paginate(10, ['*'], 'approved_jo_page')
            ->withQueryString();

        $approvedPersonalRequests = $this->applyPersonalCashAdvanceFilter((clone $baseQuery)->where('status', 'Approved'))
            ->latest()
            ->paginate(10, ['*'], 'approved_personal_page')
            ->withQueryString();

        $employees = User::query()
            ->orderBy('name')
            ->get();

        $jobOrders = JobOrder::query()
            ->select(['id', 'code', 'number', 'consignee'])
            ->whereNotNull('number')
            ->where('number', '!=', '')
            ->orderByRaw('CAST(number AS UNSIGNED) DESC')
            ->orderByDesc('number')
            ->limit(1000)
            ->get()
            ->map(fn (JobOrder $jobOrder) => [
                'value' => trim(($jobOrder->code ?? '-') . ' - ' . ($jobOrder->number ?? '-')),
                'label' => trim(($jobOrder->code ?? '-') . ' - ' . ($jobOrder->number ?? '-') . ' | ' . ($jobOrder->consignee ?? '')),
            ]);

        return view('admin.cash-advances.index', [
            'pendingJoRequests' => $pendingJoRequests,
            'pendingPersonalRequests' => $pendingPersonalRequests,
            'approvedJoRequests' => $approvedJoRequests,
            'approvedPersonalRequests' => $approvedPersonalRequests,
            'employees' => $employees,
            'jobOrders' => $jobOrders,
            'userId' => $userId,
            'nextCaNo' => $this->nextCaNo(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'ca_type' => ['required', 'in:jo,personal'],
            'ca_no' => ['nullable', 'string', 'max:50'],
            'mark_as_paid' => ['nullable', 'boolean'],
            'salary_deduction_terms' => ['nullable', 'integer', 'min:1', 'max:60'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.jo_number' => ['nullable', 'string', 'max:50', 'required_without:items.*.reason'],
            'items.*.reason' => ['nullable', 'string', 'max:255', 'required_without:items.*.jo_number'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
        ]);

        $totalAmount = collect($validated['items'])->sum('amount');
        $isPersonal = $validated['ca_type'] === 'personal';

        $hasJoItem = collect($validated['items'])->contains(fn ($item) => !empty($item['jo_number'] ?? null));
        $hasReasonItem = collect($validated['items'])->contains(fn ($item) => !empty($item['reason'] ?? null));

        if (!$isPersonal && !$hasJoItem) {
            return back()
                ->withErrors(['items' => 'Please select at least one JO for a JO Cash Advance.'])
                ->withInput();
        }

        if ($isPersonal && !$hasReasonItem) {
            return back()
                ->withErrors(['items' => 'Please add a reason for a Personal Cash Advance.'])
                ->withInput();
        }

        $markAsPaid = (bool) ($validated['mark_as_paid'] ?? false);

        $cashAdvance = CashAdvanceRequest::query()->create([
            'user_id' => $validated['user_id'],
            'ca_no' => $validated['ca_no'] ?: $this->nextCaNo(),
            'amount' => $totalAmount,
            'is_personal' => $isPersonal,
            'salary_deduction_terms' => $isPersonal ? ($validated['salary_deduction_terms'] ?? 1) : null,
            'status' => 'Approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'paid_at' => $markAsPaid ? now() : null,
            'paid_by' => $markAsPaid ? $request->user()->id : null,
        ]);

        foreach ($validated['items'] as $item) {
            $cashAdvance->items()->create([
                'jo_number' => $item['jo_number'] ?? null,
                'reason' => $item['reason'] ?? null,
                'amount' => $item['amount'],
            ]);
        }

        $cashAdvance->user->notify(new CashAdvanceReviewed('Approved'));

        return back()->with('status', 'cash-advance-created');
    }

    public function update(Request $request, CashAdvanceRequest $cashAdvanceRequest)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Approved,Rejected'],
        ]);

        $cashAdvanceRequest->update([
            'status' => $validated['status'],
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $cashAdvanceRequest->user->notify(new CashAdvanceReviewed($validated['status']));

        return back()->with('status', 'cash-advance-updated');
    }

    public function updatePersonalPaid(Request $request, CashAdvanceRequest $cashAdvanceRequest): JsonResponse
    {
        $validated = $request->validate([
            'personal_paid_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (!$cashAdvanceRequest->is_personal) {
            return response()->json(['message' => 'Only personal cash advances can be updated.'], 422);
        }

        $cashAdvanceRequest->update([
            'personal_paid_amount' => $validated['personal_paid_amount'] ?? 0,
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function destroy(CashAdvanceRequest $cashAdvanceRequest)
    {
        DB::transaction(function () use ($cashAdvanceRequest) {
            $liquidations = $cashAdvanceRequest->liquidations()->get();

            foreach ($liquidations as $liquidation) {
                $receiptPaths = $liquidation->receipt_paths ?? [];
                if (empty($receiptPaths) && !empty($liquidation->receipt_path)) {
                    $receiptPaths = [$liquidation->receipt_path];
                }

                foreach ($receiptPaths as $path) {
                    if (!empty($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            $cashAdvanceRequest->liquidations()->delete();
            $cashAdvanceRequest->items()->delete();
            $cashAdvanceRequest->delete();
        });

        return back()->with('status', 'cash-advance-deleted');
    }

    private function nextCaNo(): string
    {
        $max = CashAdvanceRequest::query()
            ->selectRaw('MAX(CAST(ca_no AS UNSIGNED)) as max_no')
            ->value('max_no');

        if (!$max) {
            return '9000';
        }

        return (string) ((int) $max + 1);
    }

    private function applyJoCashAdvanceFilter($query)
    {
        return $query->where(function ($inner) {
            $inner->where('is_personal', false)
                ->whereHas('items', function ($itemQuery) {
                    $itemQuery->whereNotNull('jo_number')
                        ->where('jo_number', '!=', '');
                });
        });
    }

    private function applyPersonalCashAdvanceFilter($query)
    {
        return $query->where(function ($inner) {
            $inner->where('is_personal', true)
                ->orWhereDoesntHave('items', function ($itemQuery) {
                    $itemQuery->whereNotNull('jo_number')
                        ->where('jo_number', '!=', '');
                });
        });
    }
}
