<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceLiquidation;
use App\Models\CashAdvanceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CashAdvanceLiquidationController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('user_id');

        $employees = \App\Models\User::query()
            ->orderBy('name')
            ->get();

        $baseQuery = CashAdvanceLiquidation::query()
            ->with(['cashAdvanceRequest.user'])
            ->when($userId, function ($query) use ($userId) {
                $query->whereHas('cashAdvanceRequest', function ($subQuery) use ($userId) {
                    $subQuery->where('user_id', $userId);
                });
            });

        $pendingLiquidations = (clone $baseQuery)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'Pending');
            })
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(25, ['*'], 'pending_page')
            ->withQueryString();

        $approvedLiquidations = (clone $baseQuery)
            ->where('status', 'Approved')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(25, ['*'], 'approved_page')
            ->withQueryString();

        return view('admin.cash-advances.liquidation-approvals', [
            'pendingLiquidations' => $pendingLiquidations,
            'approvedLiquidations' => $approvedLiquidations,
            'employees' => $employees,
            'userId' => $userId,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'cash_advance_request_id' => ['required', 'exists:cash_advance_requests,id'],
            'date' => ['required', 'date'],
            'ref_no' => ['nullable', 'string', 'max:50'],
            'jo_number' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'receipts' => ['nullable', 'array'],
            'receipts.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $hasMeaningfulInput =
            trim((string) ($validated['ref_no'] ?? '')) !== '' ||
            trim((string) ($validated['jo_number'] ?? '')) !== '' ||
            trim((string) ($validated['remarks'] ?? '')) !== '' ||
            ((float) ($validated['amount'] ?? 0)) > 0 ||
            $request->hasFile('receipts');

        // Prevent accidental auto-created liquidation rows (date-only saves from autosave).
        if (!$hasMeaningfulInput) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'skipped',
                    'message' => 'No liquidation details entered.',
                ]);
            }

            return back()->with('status', 'cash-advance-liquidation-skipped');
        }

        $requestId = (int) $validated['cash_advance_request_id'];
        $incomingDate = (string) $validated['date'];
        $incomingRefNo = $this->normalizeNullableText($validated['ref_no'] ?? null);
        $incomingJo = $this->normalizeNullableText($validated['jo_number'] ?? null);
        $incomingAmount = (float) ($validated['amount'] ?? 0);
        $incomingRemarks = $this->normalizeNullableText($validated['remarks'] ?? null);

        // Reuse exact existing liquidation for this CA request to avoid duplicate transactions.
        $existingMatch = CashAdvanceLiquidation::query()
            ->where('cash_advance_request_id', $requestId)
            ->orderByDesc('id')
            ->get()
            ->first(function (CashAdvanceLiquidation $liq) use ($incomingDate, $incomingRefNo, $incomingJo, $incomingAmount): bool {
                $liqRef = $this->normalizeNullableText($liq->ref_no);
                $liqJo = $this->normalizeNullableText($liq->jo_number);
                $sameRef = $liqRef === $incomingRefNo;
                $sameJo = $this->normalizedJoNumber($liqJo) === $this->normalizedJoNumber($incomingJo);
                $sameAmount = abs(((float) $liq->amount) - $incomingAmount) < 0.00001;
                $sameDate = optional($liq->date)->format('Y-m-d') === $incomingDate;
                return $sameRef && $sameJo && $sameAmount && $sameDate;
            });

        if ($existingMatch) {
            $existingReceiptPaths = $existingMatch->receipt_paths ?? [];
            if (empty($existingReceiptPaths) && !empty($existingMatch->receipt_path)) {
                $existingReceiptPaths = [$existingMatch->receipt_path];
            }
            $mergedReceipts = array_values(array_unique($existingReceiptPaths));

            $existingMatch->update([
                'date' => $incomingDate,
                'ref_no' => $incomingRefNo,
                'jo_number' => $incomingJo,
                'amount' => $incomingAmount,
                'remarks' => $incomingRemarks ?? $existingMatch->remarks,
                'receipt_paths' => $mergedReceipts,
                'status' => 'Approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'ok',
                    'liquidation_id' => $existingMatch->id,
                    'reused' => true,
                ]);
            }

            return back()->with('status', 'cash-advance-liquidation-created');
        }

        // Reuse same CA + same ref_no + same JO row and update it, instead of creating a second row.
        if ($incomingRefNo !== null && $incomingJo !== null) {
            $sameRefAndJo = CashAdvanceLiquidation::query()
                ->where('cash_advance_request_id', $requestId)
                ->where('ref_no', $incomingRefNo)
                ->where('jo_number', $incomingJo)
                ->latest('id')
                ->first();

            if ($sameRefAndJo) {
                $existingReceiptPaths = $sameRefAndJo->receipt_paths ?? [];
                if (empty($existingReceiptPaths) && !empty($sameRefAndJo->receipt_path)) {
                    $existingReceiptPaths = [$sameRefAndJo->receipt_path];
                }
                $sameRefAndJo->update([
                    'date' => $incomingDate,
                    'amount' => $incomingAmount,
                    'remarks' => $incomingRemarks ?? $sameRefAndJo->remarks,
                    'receipt_paths' => array_values(array_unique($existingReceiptPaths)),
                    'status' => 'Approved',
                    'approved_by' => $request->user()->id,
                    'approved_at' => now(),
                ]);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status' => 'ok',
                        'liquidation_id' => $sameRefAndJo->id,
                        'reused' => true,
                    ]);
                }

                return back()->with('status', 'cash-advance-liquidation-created');
            }
        }

        // Reuse old empty placeholder rows (from prior date-only autosaves) instead of creating another line.
        $placeholderRow = CashAdvanceLiquidation::query()
            ->where('cash_advance_request_id', $requestId)
            ->whereDate('date', $incomingDate)
            ->where(function ($query) {
                $query->whereNull('ref_no')->orWhere('ref_no', '');
            })
            ->where(function ($query) {
                $query->whereNull('jo_number')->orWhere('jo_number', '');
            })
            ->where(function ($query) {
                $query->whereNull('remarks')->orWhere('remarks', '');
            })
            ->where(function ($query) {
                $query->whereNull('amount')->orWhere('amount', '<=', 0);
            })
            ->latest('id')
            ->first();

        $receiptPaths = [];
        if ($request->hasFile('receipts')) {
            foreach ($request->file('receipts') as $file) {
                $receiptPaths[] = $file->store('cash-advance-liquidations', 'public');
            }
        }

        if ($placeholderRow) {
            $existingReceiptPaths = $placeholderRow->receipt_paths ?? [];
            if (empty($existingReceiptPaths) && !empty($placeholderRow->receipt_path)) {
                $existingReceiptPaths = [$placeholderRow->receipt_path];
            }

            $placeholderRow->update([
                'ref_no' => $incomingRefNo,
                'jo_number' => $incomingJo,
                'amount' => $incomingAmount,
                'remarks' => $incomingRemarks,
                'receipt_paths' => array_values(array_unique(array_merge($existingReceiptPaths, $receiptPaths))),
                'status' => 'Approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            $liquidation = $placeholderRow->fresh();
        } else {
            $liquidation = CashAdvanceLiquidation::create([
                'cash_advance_request_id' => $requestId,
                'date' => $incomingDate,
                'ref_no' => $incomingRefNo,
                'jo_number' => $incomingJo,
                'amount' => $incomingAmount,
                'remarks' => $incomingRemarks,
                'receipt_paths' => $receiptPaths,
                'status' => 'Approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'liquidation_id' => $liquidation->id,
            ]);
        }

        return back()->with('status', 'cash-advance-liquidation-created');
    }

    private function normalizeNullableText(mixed $value): ?string
    {
        $text = trim((string) ($value ?? ''));
        return $text === '' ? null : $text;
    }

    private function normalizedJoNumber(?string $joNumber): ?string
    {
        if ($joNumber === null || trim($joNumber) === '') {
            return null;
        }

        $raw = strtoupper(trim($joNumber));
        $collapsed = preg_replace('/\s+/', ' ', $raw) ?: $raw;
        if (preg_match('/(\d{3,6})/', $collapsed, $m)) {
            return $m[1];
        }

        return $collapsed;
    }

    public function update(Request $request, CashAdvanceLiquidation $cashAdvanceLiquidation): RedirectResponse|JsonResponse
    {
        if ($request->filled('status')) {
            $validatedStatus = $request->validate([
                'status' => ['required', 'in:Approved,Rejected'],
            ]);

            $status = $validatedStatus['status'];
            $cashAdvanceLiquidation->update([
                'status' => $status,
                'approved_by' => $status === 'Approved' ? $request->user()->id : null,
                'approved_at' => $status === 'Approved' ? now() : null,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'ok']);
            }

            return back()->with('status', 'cash-advance-liquidation-reviewed');
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'ref_no' => ['nullable', 'string', 'max:50'],
            'jo_number' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'receipts' => ['nullable', 'array'],
            'receipts.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $receiptPaths = $cashAdvanceLiquidation->receipt_paths ?? [];
        if (empty($receiptPaths) && !empty($cashAdvanceLiquidation->receipt_path)) {
            $receiptPaths = [$cashAdvanceLiquidation->receipt_path];
        }
        if ($request->hasFile('receipts')) {
            foreach ($request->file('receipts') as $file) {
                $receiptPaths[] = $file->store('cash-advance-liquidations', 'public');
            }
        }
        $receiptPaths = array_values(array_unique($receiptPaths));

        $updates = [
            'date' => $validated['date'],
            'receipt_paths' => $receiptPaths,
        ];

        if (array_key_exists('ref_no', $validated)) {
            $updates['ref_no'] = $validated['ref_no'] ?? null;
        }
        if (array_key_exists('jo_number', $validated)) {
            $updates['jo_number'] = $validated['jo_number'] ?? null;
        }
        if (array_key_exists('remarks', $validated)) {
            $updates['remarks'] = $validated['remarks'] ?? null;
        }
        if (array_key_exists('amount', $validated) && $validated['amount'] !== null) {
            $updates['amount'] = $validated['amount'];
        }

        $cashAdvanceLiquidation->update($updates);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('status', 'cash-advance-remarks-updated');
    }

    public function review(Request $request, CashAdvanceLiquidation $cashAdvanceLiquidation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Approved,Rejected'],
        ]);

        $status = $validated['status'];
        $cashAdvanceLiquidation->update([
            'status' => $status,
            'approved_by' => $status === 'Approved' ? $request->user()->id : null,
            'approved_at' => $status === 'Approved' ? now() : null,
        ]);

        return back()->with('status', 'cash-advance-liquidation-reviewed');
    }

    public function destroy(CashAdvanceLiquidation $cashAdvanceLiquidation): RedirectResponse
    {
        $receiptPaths = $cashAdvanceLiquidation->receipt_paths ?? [];
        if (empty($receiptPaths) && !empty($cashAdvanceLiquidation->receipt_path)) {
            $receiptPaths = [$cashAdvanceLiquidation->receipt_path];
        }

        foreach ($receiptPaths as $path) {
            if (!empty($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $cashAdvanceLiquidation->delete();

        return back()->with('status', 'cash-advance-liquidation-deleted');
    }

    public function destroyGrouped(Request $request, CashAdvanceRequest $cashAdvanceRequest): RedirectResponse
    {
        $validated = $request->validate([
            'ref_no' => ['required', 'string', 'max:50'],
        ]);

        $targetRefNo = trim($validated['ref_no']);
        $liquidations = CashAdvanceLiquidation::query()
            ->where('cash_advance_request_id', $cashAdvanceRequest->id)
            ->where('ref_no', $targetRefNo)
            ->where('remarks', 'like', 'Reimbursable Voucher #%')
            ->get();

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

            $liquidation->delete();
        }

        return back()->with('status', 'cash-advance-liquidation-deleted');
    }
}
