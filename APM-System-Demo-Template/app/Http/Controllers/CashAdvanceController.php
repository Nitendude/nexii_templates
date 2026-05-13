<?php

namespace App\Http\Controllers;

use App\Models\CashAdvanceRequest;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class CashAdvanceController extends Controller
{
    public function index(Request $request)
    {
        $requests = CashAdvanceRequest::query()
            ->where('user_id', $request->user()->id)
            ->with(['items', 'liquidations'])
            ->orderByDesc('created_at')
            ->get();

        $isPersonalCashAdvance = fn (CashAdvanceRequest $requestItem): bool => (bool) $requestItem->is_personal
            || $requestItem->items->every(fn ($item) => empty($item->jo_number));

        $personalCashAdvances = $requests
            ->filter($isPersonalCashAdvance)
            ->values();

        $cashAdvanceRequests = $requests
            ->reject($isPersonalCashAdvance)
            ->values();

        $approvedCashAdvances = $requests
            ->where('status', 'Approved')
            ->reject($isPersonalCashAdvance)
            ->filter(fn ($requestItem) => $requestItem->items->contains(fn ($item) => !empty($item->jo_number)))
            ->values();

        $existingJoGroups = $approvedCashAdvances
            ->sortByDesc('created_at')
            ->values()
            ->map(function ($requestItem) {
                $items = $requestItem->items->values();
                $liquidations = $this->aggregateVoucherLiquidations($requestItem->liquidations)
                    ->where('status', 'Approved')
                    ->sortBy(function ($liq) {
                        return optional($liq->date)->timestamp ?? 0;
                    })
                    ->values();

                $maxRows = max($items->count(), $liquidations->count(), 1);
                $rows = collect();

                for ($i = 0; $i < $maxRows; $i++) {
                    $item = $items[$i] ?? null;
                    $liquidation = $liquidations[$i] ?? null;
                    $caAmount = (float) ($item->amount ?? 0);
                    $liquidationAmount = (float) ($liquidation->amount ?? 0);

                    $rows->push((object) [
                        'row_index' => $i,
                        'ca_date' => $i === 0 ? $requestItem->created_at : null,
                        'ca_no' => $i === 0 ? $requestItem->ca_no : null,
                        'ca_jo_or_reason' => $item ? ($item->jo_number ?: $item->reason) : null,
                        'ca_amount' => $item ? $caAmount : null,
                        'liq_date' => $liquidation?->date,
                        'liq_ref_no' => $liquidation?->ref_no,
                        'liq_jo_no' => $liquidation?->jo_number,
                        'liq_amount' => $liquidation ? $liquidationAmount : null,
                        'remarks' => $liquidation?->remarks,
                        'difference' => $caAmount - $liquidationAmount,
                        'balance' => 0.0,
                    ]);
                }

                return (object) [
                    'request_id' => $requestItem->id,
                    'rows' => $rows,
                ];
            });

        // Compute running balance so newest rows show final/latest running balance first.
        $flatRows = $existingJoGroups->flatMap(fn ($group) => $group->rows)->values();
        $runningBalance = 0.0;
        for ($i = $flatRows->count() - 1; $i >= 0; $i--) {
            $runningBalance += (float) ($flatRows[$i]->difference ?? 0);
            $flatRows[$i]->balance = $runningBalance;
        }

        $cursor = 0;
        $existingJoGroups = $existingJoGroups->map(function ($group) use ($flatRows, &$cursor) {
            $rows = $group->rows->map(function () use ($flatRows, &$cursor) {
                return $flatRows[$cursor++];
            });

            return (object) [
                'request_id' => $group->request_id,
                'rows' => $rows,
            ];
        });

        return view('modules.cash-advance', [
            'requests' => $cashAdvanceRequests,
            'personalCashAdvances' => $personalCashAdvances,
            'existingJoGroups' => $existingJoGroups,
        ]);
    }

    private function aggregateVoucherLiquidations(Collection $liquidations): Collection
    {
        $regular = collect();
        $voucherGrouped = [];

        foreach ($liquidations as $liq) {
            $remarks = trim((string) ($liq->remarks ?? ''));
            $refNo = trim((string) ($liq->ref_no ?? ''));

            if ($refNo === '' || !preg_match('/^Reimbursable Voucher\s*#\s*/i', $remarks)) {
                $regular->push($liq);
                continue;
            }

            if (!isset($voucherGrouped[$refNo])) {
                $clone = clone $liq;
                $clone->amount = (float) ($liq->amount ?? 0);
                $clone->remarks = 'Reimbursable Voucher #' . $refNo;
                $voucherGrouped[$refNo] = $clone;
                continue;
            }

            $voucherGrouped[$refNo]->amount = (float) ($voucherGrouped[$refNo]->amount ?? 0) + (float) ($liq->amount ?? 0);

            if (($liq->date?->timestamp ?? 0) > ($voucherGrouped[$refNo]->date?->timestamp ?? 0)) {
                $voucherGrouped[$refNo]->date = $liq->date;
            }
        }

        return $regular
            ->concat(collect($voucherGrouped)->values())
            ->sortBy(function ($liq) {
                return optional($liq->date)->timestamp ?? 0;
            })
            ->values();
    }

    public function store(Request $request)
    {
        return redirect()
            ->to(route('cash-advances', [], false))
            ->withErrors(['cash_advance' => 'Cash advances can only be created by Admin or Accounting.']);
    }
}
