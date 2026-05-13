<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceLiquidation;
use App\Models\CashAdvanceRequest;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashAdvanceSummaryController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        $month = $request->query('month');
        $year = $request->query('year');

        $employees = User::query()
            ->orderBy('name')
            ->get();

        $selectedEmployee = null;
        $joGroups = [];
        $personalGroups = [];
        $totalsJo = [
            'ca_total' => 0,
            'liquidation_total' => 0,
            'balance' => 0,
        ];
        $totalsPersonal = [
            'ca_total' => 0,
            'balance' => 0,
        ];

        if ($userId) {
            $selectedEmployee = User::query()->findOrFail($userId);

            $requestsQuery = CashAdvanceRequest::query()
                ->where('user_id', $userId)
                ->with(['items', 'liquidations'])
                ->where('status', 'Approved');

            if (!empty($fromDate)) {
                $requestsQuery->whereDate('created_at', '>=', $fromDate);
            }
            if (!empty($toDate)) {
                $requestsQuery->whereDate('created_at', '<=', $toDate);
            }
            if (!empty($month)) {
                $requestsQuery->whereMonth('created_at', $month);
            }
            if (!empty($year)) {
                $requestsQuery->whereYear('created_at', $year);
            }

            $requests = $requestsQuery
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get();

            $runningBalanceJo = 0;
            foreach ($requests as $requestItem) {
                $items = $requestItem->items;
                $liquidations = $requestItem->liquidations
                    ->sortBy(function ($liq) {
                        return optional($liq->date)->timestamp ?? 0;
                    })
                    ->values();
                $isPersonal = (bool) $requestItem->is_personal;

                $requestTotal = $items->sum('amount');
                if ($isPersonal) {
                    $paidAmount = (float) ($requestItem->personal_paid_amount ?? 0);
                    $difference = $requestTotal - $paidAmount;

                    $totalsPersonal['ca_total'] += $requestTotal;
                    $totalsPersonal['balance'] += $difference;

                    $personalGroups[] = [
                        'request' => $requestItem,
                        'items' => $items,
                        'difference' => $difference,
                        'paid' => $paidAmount,
                    ];
                } else {
                    $liquidationTotal = $liquidations->sum('amount');
                    $difference = $requestTotal - $liquidationTotal;
                    $runningBalanceJo += $difference;

                    $totalsJo['ca_total'] += $requestTotal;
                    $totalsJo['liquidation_total'] += $liquidationTotal;
                    $totalsJo['balance'] = $runningBalanceJo;

                    $joGroups[] = [
                        'request' => $requestItem,
                        'items' => $items,
                        'liquidations' => $liquidations,
                        'difference' => $difference,
                        'balance' => $runningBalanceJo,
                    ];
                }
            }
        }

        return view('admin.cash-advances.summary', [
            'employees' => $employees,
            'selectedEmployee' => $selectedEmployee,
            'joGroups' => $joGroups,
            'personalGroups' => $personalGroups,
            'totalsJo' => $totalsJo,
            'totalsPersonal' => $totalsPersonal,
            'userId' => $userId,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function importLedger(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'ledger_text' => ['required', 'string'],
        ]);

        $lines = preg_split('/\r\n|\n|\r/', $validated['ledger_text']) ?: [];
        $currentRequest = null;
        $currentLiqDate = null;
        $touchedRequestIds = [];
        $createdRequests = 0;
        $createdItems = 0;
        $createdLiquidations = 0;

        DB::transaction(function () use (
            $lines,
            $validated,
            &$currentRequest,
            &$currentLiqDate,
            &$touchedRequestIds,
            &$createdRequests,
            &$createdItems,
            &$createdLiquidations
        ) {
            foreach ($lines as $line) {
                if (!is_string($line)) {
                    continue;
                }

                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                $upper = strtoupper($line);
                if (str_contains($upper, 'LIQUIDATION') || str_contains($upper, 'C.A NO.') || str_contains($upper, 'R.E.F')) {
                    continue;
                }

                $columns = $this->splitLedgerColumns($line);

                $caDateRaw = $columns[0];
                $caNo = $columns[1];
                $caField = $columns[2];
                $caAmount = $this->parseAmount($columns[3]);
                $liqDateRaw = $columns[4];
                $refNo = $columns[5];
                $liqField = $columns[6];
                $liqAmount = $this->parseAmount($columns[7]);
                $remarks = $columns[10] !== '' ? $columns[10] : null;

                // Recovery for space-split continuation rows where liquidation fields shifted into CA columns.
                if (
                    $currentRequest &&
                    $caNo !== '' &&
                    $caField !== '' &&
                    $caAmount > 0 &&
                    $liqDateRaw === '' &&
                    $refNo === '' &&
                    $liqField === '' &&
                    $liqAmount <= 0 &&
                    $this->parseDate($caDateRaw)
                ) {
                    $liqDateRaw = $caDateRaw;
                    $refNo = $caNo;
                    $liqField = $caField;
                    $liqAmount = $caAmount;
                    $caDateRaw = '';
                    $caNo = '';
                    $caField = '';
                    $caAmount = 0.0;
                }

                if ($caNo !== '' && $this->isValidCaNo($caNo)) {
                    $parsedCaDate = $this->parseDate($caDateRaw);
                    $createdAt = $parsedCaDate
                        ?? $this->parseDate($liqDateRaw)
                        ?? now()->startOfDay();

                    $requestRecord = CashAdvanceRequest::query()->firstOrCreate(
                        [
                            'user_id' => (int) $validated['user_id'],
                            'ca_no' => $caNo,
                        ],
                        [
                            'amount' => 0,
                            'status' => 'Approved',
                            'approved_at' => $createdAt,
                            'paid_at' => $createdAt,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ]
                    );

                    if ($requestRecord->wasRecentlyCreated) {
                        $createdRequests++;
                    } else {
                        $updates = [
                            'status' => 'Approved',
                            'approved_at' => $requestRecord->approved_at ?? $createdAt,
                            'paid_at' => $requestRecord->paid_at ?? $createdAt,
                        ];

                        // Keep historical CA request date aligned with ledger import date.
                        if ($parsedCaDate) {
                            $updates['created_at'] = $parsedCaDate;
                        }

                        $requestRecord->update($updates);
                    }

                    $currentRequest = $requestRecord;
                    $currentLiqDate = null;
                    $touchedRequestIds[$requestRecord->id] = true;
                }

                if ($currentRequest && $caAmount > 0 && $caField !== '') {
                    [$joNumber, $reason] = $this->splitJoAndReason($caField);

                    $itemExists = $currentRequest->items()
                        ->where('jo_number', $joNumber)
                        ->where('reason', $reason)
                        ->where('amount', $caAmount)
                        ->exists();

                    if (!$itemExists) {
                        $currentRequest->items()->create([
                            'jo_number' => $joNumber,
                            'reason' => $reason,
                            'amount' => $caAmount,
                            'remarks' => $remarks,
                        ]);
                        $createdItems++;
                    }
                }

                if ($currentRequest && $liqAmount <= 0 && $caNo === '' && trim($liqDateRaw) === '') {
                    $liqAmount = $this->extractContinuationLiquidationAmount($line);
                }

                if ($currentRequest && $liqAmount > 0) {
                    [$liqJoNumber, $liqReason] = $this->splitJoAndReason($liqField);
                    $liqDate = $this->parseDate($liqDateRaw)
                        ?? $currentLiqDate
                        ?? $this->parseDate($caDateRaw)
                        ?? Carbon::parse($currentRequest->created_at)->startOfDay();
                    $currentLiqDate = $liqDate;

                    $liqExists = CashAdvanceLiquidation::query()
                        ->where('cash_advance_request_id', $currentRequest->id)
                        ->whereDate('date', $liqDate->toDateString())
                        ->where('ref_no', $refNo !== '' ? $refNo : null)
                        ->where('jo_number', $liqJoNumber ?? $liqReason)
                        ->where('amount', $liqAmount)
                        ->exists();

                    if (!$liqExists) {
                        CashAdvanceLiquidation::query()->create([
                            'cash_advance_request_id' => $currentRequest->id,
                            'date' => $liqDate->toDateString(),
                            'ref_no' => $refNo !== '' ? $refNo : null,
                            'jo_number' => $liqJoNumber ?? $liqReason,
                            'amount' => $liqAmount,
                            'remarks' => $remarks,
                        ]);
                        $createdLiquidations++;
                    }
                }
            }

            foreach (array_keys($touchedRequestIds) as $requestId) {
                $requestRecord = CashAdvanceRequest::query()->with('items')->find($requestId);
                if (!$requestRecord) {
                    continue;
                }
                $requestRecord->update([
                    'amount' => $requestRecord->items->sum('amount'),
                    'status' => 'Approved',
                    'paid_at' => $requestRecord->paid_at ?? $requestRecord->created_at,
                ]);

                // Keep display date aligned with historical approval date when import produced "today" created_at.
                if ($requestRecord->approved_at && $requestRecord->created_at && $requestRecord->created_at->gt($requestRecord->approved_at)) {
                    DB::table('cash_advance_requests')
                        ->where('id', $requestRecord->id)
                        ->update(['created_at' => $requestRecord->approved_at]);
                }
            }
        });

        return redirect()->route('accounting.cash-advances.summary', [
            'user_id' => $validated['user_id'],
        ])->with('status', "Ledger import complete. Added {$createdRequests} CA, {$createdItems} items, {$createdLiquidations} liquidations.");
    }

    private function parseAmount(?string $value): float
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return 0.0;
        }
        $normalized = str_replace([',', '(', ')'], ['', '-', ''], $raw);
        return is_numeric($normalized) ? (float) $normalized : 0.0;
    }

    private function parseDate(?string $value): ?Carbon
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        // Reject non-date numeric strings (e.g. "0", "0.00", "1234.56") that can parse to unix epoch.
        if (preg_match('/^\d+(\.\d+)?$/', $raw)) {
            return null;
        }

        $formats = ['n/j/Y', 'm/d/Y', 'n/j/y', 'm/d/y', 'd/m/Y', 'd/m/y', 'Y-m-d'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $raw)->startOfDay();
            } catch (\Throwable $e) {
            }
        }

        return null;
    }

    private function splitJoAndReason(?string $value): array
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return [null, null];
        }

        if (preg_match('/^\d+[A-Za-z\-\/]*$/', $raw)) {
            return [$raw, null];
        }

        if (preg_match('/\d/', $raw) && !preg_match('/[A-Za-z]/', $raw)) {
            return [$raw, null];
        }

        return [null, $raw];
    }

    private function isValidCaNo(string $value): bool
    {
        $raw = trim($value);
        if ($raw === '') {
            return false;
        }

        // Reject decimal-like values commonly found in amount columns.
        if (preg_match('/^\d+\.\d+$/', $raw)) {
            return false;
        }

        // Accept CA numbers like 6718, 9466-A, CA 9066 - B, 9982 B.
        return (bool) preg_match('/^[A-Za-z0-9#\-\s]+$/', $raw);
    }

    private function splitLedgerColumns(string $line): array
    {
        // Preserve empty cells for TSV rows so liquidation-only lines keep amount in the correct column.
        if (str_contains($line, "\t")) {
            $columns = explode("\t", $line);
        } else {
            // Fallback for copied text without tabs.
            $columns = preg_split('/\s{2,}/', $line) ?: [];
        }

        $columns = array_map(fn ($value) => trim((string) $value), $columns);
        return array_pad($columns, 11, '');
    }

    private function extractContinuationLiquidationAmount(string $line): float
    {
        preg_match_all('/-?\d{1,3}(?:,\d{3})*(?:\.\d+)?|-?\d+\.\d+/', $line, $matches);
        $numbers = array_map(function ($token) {
            $normalized = str_replace(',', '', (string) $token);
            return is_numeric($normalized) ? (float) $normalized : 0.0;
        }, $matches[0] ?? []);

        $numbers = array_values(array_filter($numbers, fn ($n) => abs($n) > 0.00001));
        if (count($numbers) < 1) {
            return 0.0;
        }

        // Typical continuation: "<liq amount> <negative difference> <balance>"
        if (count($numbers) >= 2 && $numbers[0] > 0 && $numbers[1] < 0) {
            return $numbers[0];
        }

        return $numbers[0] > 0 ? $numbers[0] : 0.0;
    }
}
