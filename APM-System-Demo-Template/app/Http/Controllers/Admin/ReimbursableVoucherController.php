<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingStatement;
use App\Models\CashAdvanceLiquidation;
use App\Models\CashAdvanceRequest;
use App\Models\DebitCreditNote;
use App\Models\JobOrder;
use App\Models\ReimbursableVoucher;
use App\Models\ServiceInvoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReimbursableVoucherController extends Controller
{
    private const E_PAYMENT_PAYEE = 'E-Payment';
    private const TRUCKING_PAYEE = 'Trucking';

    public function index()
    {
        $vouchers = ReimbursableVoucher::query()
            ->withCount('items')
            ->with(['creator', 'items'])
            ->orderByDesc('voucher_date')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.reimbursable-vouchers.index', [
            'vouchers' => $vouchers,
        ]);
    }

    public function create()
    {
        return view('admin.reimbursable-vouchers.create', [
            'voucherJobOrders' => $this->voucherJobOrders(),
            'costSheetAmountsByJoNumber' => $this->costSheetAmountsByJoNumber(),
            'employeePayees' => $this->employeePayees(),
            'nextVoucherNo' => $this->nextVoucherNo(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateVoucherRequest($request);
        [$rows, $total] = $this->prepareVoucherRows($validated);

        $voucher = DB::transaction(function () use ($request, $validated, $rows, $total) {
            $voucherNo = trim((string) ($validated['voucher_no'] ?? '')) ?: $this->nextVoucherNo();

            $rows = array_map(function (array $row) use ($voucherNo) {
                $row['rv_cv_no'] = trim((string) ($row['rv_cv_no'] ?? '')) ?: $voucherNo;

                return $row;
            }, $rows);

            $voucher = ReimbursableVoucher::create([
                'voucher_no' => $voucherNo,
                'voucher_date' => $validated['voucher_date'],
                'payee' => trim((string) ($validated['voucher_payee'] ?? '')) ?: null,
                'ref_no' => trim((string) ($validated['voucher_ref_no'] ?? '')) ?: null,
                'total_amount' => $total,
                'amount_in_words' => $validated['amount_in_words'] ?? $this->numberToWords($total),
                'prepared_by' => 'M.A.S / D.L.C / K.A.P / R.J.R',
                'approved_by' => 'A.P.M',
                'received_payment' => null,
                'created_by' => $request->user()?->id,
            ]);

            $voucher->items()->createMany($rows);
            $this->createLiquidationsFromVoucherRows($request, $voucherNo, $rows);

            return $voucher;
        });

        return redirect()
            ->route('accounting.reimbursable-vouchers.show', $voucher)
            ->with('status', 'reimbursable-voucher-saved');
    }

    public function edit(ReimbursableVoucher $reimbursableVoucher)
    {
        if ($this->isCancelled($reimbursableVoucher)) {
            return redirect()
                ->route('accounting.reimbursable-vouchers.show', $reimbursableVoucher)
                ->with('status', 'reimbursable-voucher-cancelled-readonly');
        }

        $reimbursableVoucher->load('items');

        return view('admin.reimbursable-vouchers.create', [
            'voucher' => $reimbursableVoucher,
            'voucherJobOrders' => $this->voucherJobOrders(),
            'costSheetAmountsByJoNumber' => $this->costSheetAmountsByJoNumber(),
            'employeePayees' => $this->employeePayees(),
            'nextVoucherNo' => $this->nextVoucherNo(),
        ]);
    }

    public function update(Request $request, ReimbursableVoucher $reimbursableVoucher)
    {
        if ($this->isCancelled($reimbursableVoucher)) {
            return redirect()
                ->route('accounting.reimbursable-vouchers.show', $reimbursableVoucher)
                ->with('status', 'reimbursable-voucher-cancelled-readonly');
        }

        $validated = $this->validateVoucherRequest($request, $reimbursableVoucher);
        [$rows, $total] = $this->prepareVoucherRows($validated);

        DB::transaction(function () use ($request, $reimbursableVoucher, $validated, $rows, $total) {
            $oldVoucherNo = trim((string) $reimbursableVoucher->voucher_no);
            $voucherNo = trim((string) ($validated['voucher_no'] ?? '')) ?: $oldVoucherNo;

            $this->deleteVoucherLiquidations($oldVoucherNo);

            $rows = array_map(function (array $row) use ($voucherNo) {
                $row['rv_cv_no'] = trim((string) ($row['rv_cv_no'] ?? '')) ?: $voucherNo;

                return $row;
            }, $rows);

            $reimbursableVoucher->update([
                'voucher_no' => $voucherNo,
                'voucher_date' => $validated['voucher_date'],
                'payee' => trim((string) ($validated['voucher_payee'] ?? '')) ?: null,
                'ref_no' => trim((string) ($validated['voucher_ref_no'] ?? '')) ?: null,
                'total_amount' => $total,
                'amount_in_words' => $validated['amount_in_words'] ?? $this->numberToWords($total),
            ]);

            $reimbursableVoucher->items()->delete();
            $reimbursableVoucher->items()->createMany($rows);
            $this->createLiquidationsFromVoucherRows($request, $voucherNo, $rows);
        });

        return redirect()
            ->route('accounting.reimbursable-vouchers.show', $reimbursableVoucher)
            ->with('status', 'reimbursable-voucher-updated');
    }

    public function show(ReimbursableVoucher $reimbursableVoucher)
    {
        $reimbursableVoucher->load('items', 'creator', 'canceller');

        return view('admin.reimbursable-vouchers.show', [
            'voucher' => $reimbursableVoucher,
        ]);
    }

    public function cancel(Request $request, ReimbursableVoucher $reimbursableVoucher)
    {
        if ($this->isCancelled($reimbursableVoucher)) {
            return redirect()
                ->route('accounting.reimbursable-vouchers.show', $reimbursableVoucher)
                ->with('status', 'reimbursable-voucher-already-cancelled');
        }

        DB::transaction(function () use ($request, $reimbursableVoucher): void {
            $originalVoucherNo = trim((string) $reimbursableVoucher->voucher_no);
            $this->deleteVoucherLiquidations($originalVoucherNo);

            $reimbursableVoucher->update([
                'cancelled_voucher_no' => $originalVoucherNo,
                'voucher_no' => $this->cancelledStorageVoucherNo($reimbursableVoucher->id),
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now(),
                'cancelled_by' => $request->user()?->id,
            ]);
        });

        return redirect()
            ->route('accounting.reimbursable-vouchers.index')
            ->with('status', 'reimbursable-voucher-cancelled');
    }

    private function nextVoucherNo(): string
    {
        $max = (int) ReimbursableVoucher::query()
            ->selectRaw('MAX(CAST(voucher_no AS UNSIGNED)) as max_no')
            ->value('max_no');

        if ($max < 8609) {
            return '8609';
        }

        return (string) ($max + 1);
    }

    private function employeePayees()
    {
        return User::query()
            ->where('role', 'employee')
            ->whereIn('status', ['Active', 'On Leave'])
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function validateVoucherRequest(Request $request, ?ReimbursableVoucher $voucher = null): array
    {
        $request->merge([
            'amount' => collect($request->input('amount', []))
                ->map(fn ($amount) => is_string($amount) ? str_replace(',', '', $amount) : $amount)
                ->all(),
        ]);

        $employeeNames = $this->employeePayees()
            ->pluck('name')
            ->push(self::E_PAYMENT_PAYEE)
            ->push(self::TRUCKING_PAYEE)
            ->all();
        $voucherNoRule = Rule::unique('reimbursable_vouchers', 'voucher_no');
        if ($voucher) {
            $voucherNoRule->ignore($voucher->id);
        }

        return $request->validate([
            'voucher_no' => ['nullable', 'string', 'max:20', $voucherNoRule],
            'voucher_date' => ['required', 'date'],
            'voucher_payee' => ['nullable', 'string', 'max:80'],
            'voucher_ref_no' => ['nullable', 'string', 'max:120'],
            'jo_no' => ['nullable', 'array'],
            'jo_no.*' => ['nullable', 'string', 'max:120'],
            'client' => ['nullable', 'array'],
            'client.*' => ['nullable', 'string', 'max:255'],
            'payee' => ['nullable', 'array'],
            'payee.*' => ['nullable', 'string', 'max:255', Rule::in($employeeNames)],
            'deduct_ca' => ['nullable', 'array'],
            'deduct_ca.*' => ['nullable', Rule::in(['0', '1', 0, 1])],
            'deduction_type' => ['nullable', 'array'],
            'deduction_type.*' => ['nullable', Rule::in(['none', 'advance', 'penalty'])],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string', 'max:255'],
            'liq_no' => ['nullable', 'array'],
            'liq_no.*' => ['nullable', 'string', 'max:120'],
            'rv_cv_no' => ['nullable', 'array'],
            'rv_cv_no.*' => ['nullable', 'string', 'max:120'],
            'remarks' => ['nullable', 'array'],
            'remarks.*' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'array'],
            'amount.*' => ['nullable', 'numeric', 'min:0'],
            'amount_in_words' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function cancelledStorageVoucherNo(int $voucherId): string
    {
        return 'CAN-' . $voucherId;
    }

    private function isCancelled(ReimbursableVoucher $voucher): bool
    {
        return strtolower((string) ($voucher->status ?? 'active')) === 'cancelled';
    }

    private function prepareVoucherRows(array $validated): array
    {
        $joNos = $validated['jo_no'] ?? [];
        $clients = $validated['client'] ?? [];
        $payees = $validated['payee'] ?? [];
        $deductCaFlags = $validated['deduct_ca'] ?? [];
        $deductionTypes = $validated['deduction_type'] ?? [];
        $descriptions = $validated['description'] ?? [];
        $liqNos = $validated['liq_no'] ?? [];
        $rvCvNos = $validated['rv_cv_no'] ?? [];
        $remarksList = $validated['remarks'] ?? [];
        $amounts = $validated['amount'] ?? [];

        $max = max(
            count($joNos),
            count($clients),
            count($payees),
            count($deductCaFlags),
            count($deductionTypes),
            count($descriptions),
            count($liqNos),
            count($rvCvNos),
            count($remarksList),
            count($amounts)
        );
        $rows = [];
        $total = 0.0;
        $activeJoNo = null;
        $activeClient = null;
        $activePayee = null;
        $activeLiqNo = null;

        for ($i = 0; $i < $max; $i++) {
            $joNo = trim((string) ($joNos[$i] ?? ''));
            $client = trim((string) ($clients[$i] ?? ''));
            $payee = trim((string) ($payees[$i] ?? ''));
            $deductCa = (string) ($deductCaFlags[$i] ?? '1');
            $deductionType = strtolower(trim((string) ($deductionTypes[$i] ?? 'none')));
            if (!in_array($deductionType, ['none', 'advance', 'penalty'], true)) {
                $deductionType = 'none';
            }
            $description = trim((string) ($descriptions[$i] ?? ''));
            if ($deductionType === 'none') {
                $deductionType = $this->inferDeductionTypeFromDescription($description);
            }
            if ($description === '' && $deductionType === 'advance') {
                $description = 'LESS: ADVANCES';
            } elseif ($description === '' && $deductionType === 'penalty') {
                $description = 'LESS: PENALTY';
            }
            $liqNo = trim((string) ($liqNos[$i] ?? ''));
            $rvCvNo = trim((string) ($rvCvNos[$i] ?? ''));
            $remarks = trim((string) ($remarksList[$i] ?? ''));
            $amount = (float) ($amounts[$i] ?? 0);

            if ($joNo === '' && $client === '' && $description === '' && $liqNo === '' && $rvCvNo === '' && $remarks === '' && $amount <= 0) {
                continue;
            }

            if ($joNo !== '') {
                $activeJoNo = $joNo;
            } elseif ($description !== '' || $payee !== '' || $rvCvNo !== '' || $remarks !== '' || $amount > 0 || $liqNo !== '' || $client !== '') {
                $joNo = $activeJoNo ?? '';
            }

            if ($client !== '') {
                $activeClient = $client;
            } elseif ($joNo !== '' && $activeJoNo === $joNo && $activeClient !== null) {
                $client = $activeClient;
            }

            if ($payee !== '') {
                $activePayee = $payee;
            } elseif ($joNo !== '' && $activeJoNo === $joNo && $activePayee !== null) {
                $payee = $activePayee;
            }

            if ($liqNo !== '') {
                $activeLiqNo = $liqNo;
            } elseif ($joNo !== '' && $activeJoNo === $joNo && $activeLiqNo !== null) {
                $liqNo = $activeLiqNo;
            }

            $rows[] = [
                'line_no' => $i + 1,
                'jo_no' => $joNo !== '' ? $joNo : null,
                'client_name' => $client !== '' ? $client : null,
                'payee' => $payee !== '' ? $payee : null,
                'deduct_ca' => $deductCa !== '0',
                'deduction_type' => $deductionType,
                'description' => $description !== '' ? $description : null,
                'liq_no' => $liqNo !== '' ? $liqNo : null,
                'rv_cv_no' => $rvCvNo !== '' ? $rvCvNo : null,
                'remarks' => $remarks !== '' ? $remarks : null,
                'amount' => $amount,
            ];
            $signedAmount = $deductionType === 'penalty' ? -abs($amount) : $amount;
            $total += $signedAmount;
        }

        if (count($rows) === 0) {
            throw ValidationException::withMessages([
                'jo_no' => 'Add at least one voucher line before saving.',
            ]);
        }

        return [$rows, $total];
    }

    private function inferDeductionTypeFromDescription(?string $description): string
    {
        $value = strtoupper(trim((string) $description));
        if ($value === '') {
            return 'none';
        }

        if (str_contains($value, 'PENALTY')) {
            return 'penalty';
        }

        if (str_contains($value, 'ADVANCE')) {
            return 'advance';
        }

        return 'none';
    }

    private function deleteVoucherLiquidations(string $voucherNo): void
    {
        if ($voucherNo === '') {
            return;
        }

        CashAdvanceLiquidation::query()
            ->where('ref_no', $voucherNo)
            ->where('remarks', 'like', 'Reimbursable Voucher #' . $voucherNo . '%')
            ->delete();
    }

    private function createLiquidationsFromVoucherRows(Request $request, string $voucherNo, array $rows): void
    {
        $employeesByName = $this->employeePayees()
            ->keyBy(fn ($employee) => strtolower(trim((string) $employee->name)));

        collect($rows)
            ->filter(fn (array $row) => !empty($row['deduct_ca']) && !empty($row['payee']) && !$this->isEPaymentPayee($row['payee']) && strcasecmp(trim((string) $row['payee']), self::TRUCKING_PAYEE) !== 0 && !empty($row['jo_no']) && (float) ($row['amount'] ?? 0) > 0)
            ->each(function (array $row) use ($employeesByName, $request, $voucherNo): void {
                $payeeName = strtolower(trim((string) ($row['payee'] ?? '')));
                $employee = $employeesByName->get($payeeName);
                if (!$employee) {
                    return;
                }

                $joNumber = $this->extractJoNumber($row['jo_no']);
                if (!$joNumber) {
                    return;
                }

                // Reflect only to existing approved JO CAs to avoid creating unintended CA amounts.
                $cashAdvance = $this->findApprovedJoCashAdvance($employee->id, $joNumber, (float) ($row['amount'] ?? 0));
                if (!$cashAdvance) {
                    return;
                }

                $exists = CashAdvanceLiquidation::query()
                    ->where('cash_advance_request_id', $cashAdvance->id)
                    ->where('ref_no', $voucherNo)
                    ->where('jo_number', $row['jo_no'])
                    ->where('amount', (float) ($row['amount'] ?? 0))
                    ->exists();

                if ($exists) {
                    return;
                }

                CashAdvanceLiquidation::create([
                    'cash_advance_request_id' => $cashAdvance->id,
                    'date' => now()->toDateString(),
                    'ref_no' => $voucherNo,
                    'jo_number' => $row['jo_no'],
                    'amount' => $row['amount'],
                    'remarks' => trim('Reimbursable Voucher #' . $voucherNo . ' ' . ($row['description'] ?? '')),
                    'status' => 'Approved',
                    'approved_by' => $request->user()?->id,
                    'approved_at' => now(),
                ]);
            });

    }

    private function findApprovedJoCashAdvance(int $employeeId, string $joNumber, float $targetAmount = 0): ?CashAdvanceRequest
    {
        $requests = CashAdvanceRequest::query()
            ->where('user_id', $employeeId)
            ->where('status', 'Approved')
            ->where('is_personal', false)
            ->whereHas('items', function ($query) use ($joNumber) {
                $query->where('jo_number', 'like', "%{$joNumber}%");
            })
            ->with('items')
            ->latest('approved_at')
            ->latest('id')
            ->get();

        if ($requests->isEmpty()) {
            return null;
        }

        if ($targetAmount <= 0) {
            return $requests->first();
        }

        $best = null;
        $bestScore = PHP_FLOAT_MAX;

        foreach ($requests as $request) {
            $matchingItemAmounts = $request->items
                ->filter(function ($item) use ($joNumber) {
                    return str_contains((string) $item->jo_number, $joNumber);
                })
                ->pluck('amount')
                ->map(fn ($amount) => (float) $amount);

            if ($matchingItemAmounts->isEmpty()) {
                continue;
            }

            $closestItemDiff = $matchingItemAmounts
                ->map(fn (float $amount) => abs($amount - $targetAmount))
                ->min();

            $existingLinkedForSameJo = CashAdvanceLiquidation::query()
                ->where('cash_advance_request_id', $request->id)
                ->where('jo_number', 'like', "%{$joNumber}%")
                ->sum('amount');

            $requestItemTotalForSameJo = $matchingItemAmounts->sum();
            $postLinkDelta = abs(($requestItemTotalForSameJo - ((float) $existingLinkedForSameJo + $targetAmount)));

            // Prefer: closest item amount, then best remaining balance fit.
            $score = ($closestItemDiff * 1000000) + $postLinkDelta;

            if ($score < $bestScore) {
                $bestScore = $score;
                $best = $request;
            }
        }

        return $best ?: $requests->first();
    }

    private function isEPaymentPayee(?string $payee): bool
    {
        return strcasecmp(trim((string) $payee), self::E_PAYMENT_PAYEE) === 0;
    }

    private function shouldPrefillVoucherDescription(string $remarks): bool
    {
        $normalized = strtoupper(trim($remarks));
        if ($normalized === '') {
            return false;
        }

        return !str_starts_with($normalized, 'IMPORTED ')
            && !str_contains($normalized, ' CA LEDGER')
            && !str_starts_with($normalized, 'REIMBURSABLE VOUCHER #');
    }

    private function voucherJobOrders()
    {
        $jobOrders = JobOrder::query()
            ->select(['id', 'code', 'number', 'consignee'])
            ->orderByDesc('created_at')
            ->limit(500)
            ->get();

        $latestLiquidationsByJo = [];
        $liquidations = CashAdvanceLiquidation::query()
            ->where('status', 'Approved')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get(['jo_number', 'ref_no', 'amount', 'remarks']);

        foreach ($liquidations as $liquidation) {
            $joNumber = $this->extractJoNumber($liquidation->jo_number);
            if (!$joNumber || isset($latestLiquidationsByJo[$joNumber])) {
                continue;
            }

            $remarks = trim((string) ($liquidation->remarks ?? ''));
            $latestLiquidationsByJo[$joNumber] = [
                'liq_no' => trim((string) ($liquidation->ref_no ?? '')),
                'amount' => number_format((float) ($liquidation->amount ?? 0), 2, '.', ''),
                'description' => $this->shouldPrefillVoucherDescription($remarks) ? $remarks : '',
            ];
        }

        return $jobOrders
            ->map(function (JobOrder $jo) use ($latestLiquidationsByJo) {
                $consignee = trim((string) ($jo->consignee ?? ''));
                $clientShort = $this->voucherClientLabel($consignee);

                $joNumber = trim((string) ($jo->number ?? ''));
                $liquidationPrefill = $latestLiquidationsByJo[$joNumber] ?? null;

                return [
                    'id' => $jo->id,
                    'jo_number' => $joNumber,
                    'jo_no' => trim(($jo->code ?? '-') . ' - ' . ($jo->number ?? '-')),
                    'consignee' => $consignee,
                    'client' => $clientShort,
                    'label' => trim(($jo->code ?? '-') . ' - ' . ($jo->number ?? '-')),
                    'prefill_liq_no' => $liquidationPrefill['liq_no'] ?? '',
                    'prefill_description' => $liquidationPrefill['description'] ?? '',
                ];
            });
    }

    private function costSheetAmountsByJoNumber(): array
    {
        $jobOrders = JobOrder::query()
            ->select(['id', 'number'])
            ->whereNotNull('number')
            ->where('number', '!=', '')
            ->get();

        $jobOrderIdsByNumber = $jobOrders
            ->mapWithKeys(fn (JobOrder $jobOrder) => [trim((string) $jobOrder->number) => $jobOrder->id]);

        $jobOrderIds = $jobOrderIdsByNumber->values()->unique()->all();

        $billingLatestByJobOrderId = BillingStatement::query()
            ->select(['job_order_id', 'data', 'created_at'])
            ->whereIn('job_order_id', $jobOrderIds)
            ->where('document_type', '!=', 'service_invoice')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('job_order_id');

        $serviceLatestByJobOrderId = ServiceInvoice::query()
            ->select(['job_order_id', 'data', 'created_at'])
            ->whereIn('job_order_id', $jobOrderIds)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('job_order_id');

        $noteLatestByJobOrderId = DebitCreditNote::query()
            ->select(['job_order_id', 'data', 'created_at'])
            ->whereIn('job_order_id', $jobOrderIds)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('job_order_id');

        $amountsByJoNumber = [];

        foreach ($jobOrderIdsByNumber as $joNumber => $jobOrderId) {
            $latestBilling = optional($billingLatestByJobOrderId->get($jobOrderId))->first();
            $latestService = optional($serviceLatestByJobOrderId->get($jobOrderId))->first();
            $latestNote = optional($noteLatestByJobOrderId->get($jobOrderId))->first();

            $billingData = is_array($latestBilling?->data) ? $latestBilling->data : [];
            $serviceData = is_array($latestService?->data) ? $latestService->data : [];
            $noteData = is_array($latestNote?->data) ? $latestNote->data : [];

            $amountsByJoNumber[$joNumber] = $this->buildCostInfoAmounts(
                $billingData,
                $serviceData,
                $noteData
            );
        }

        return $amountsByJoNumber;
    }

    private function voucherClientLabel(?string $clientName): string
    {
        $clientName = trim((string) $clientName);
        if ($clientName === '' || mb_strlen($clientName) <= 20) {
            return $clientName;
        }

        $words = preg_split('/\s+/', $clientName) ?: [];
        $words = array_values(array_filter($words, fn ($word) => trim((string) $word) !== ''));

        return trim(implode(' ', array_slice($words, 0, 2))) ?: $clientName;
    }

    private function buildCostInfoAmounts(array $billingData, array $serviceData, array $noteData): array
    {
        $totals = [];

        $appendAmount = function (?string $description, float $amount) use (&$totals): void {
            $key = $this->normalizeCostDescription($description);
            if ($key === null || abs($amount) < 0.00001) {
                return;
            }

            $totals[$key] = round((float) (($totals[$key] ?? 0.0) + $amount), 2);
        };

        $nonDesc = $billingData['non_receipted_desc'] ?? [];
        $nonAmt = $billingData['non_receipted_amount'] ?? [];
        $nonRows = max(is_countable($nonDesc) ? count($nonDesc) : 0, is_countable($nonAmt) ? count($nonAmt) : 0);
        for ($i = 0; $i < $nonRows; $i++) {
            $appendAmount($nonDesc[$i] ?? null, (float) ($nonAmt[$i] ?? 0));
        }

        $recDesc = $billingData['receipted_desc'] ?? [];
        $recAmt = $billingData['receipted_amount'] ?? [];
        $recRows = max(is_countable($recDesc) ? count($recDesc) : 0, is_countable($recAmt) ? count($recAmt) : 0);
        for ($i = 0; $i < $recRows; $i++) {
            $appendAmount($recDesc[$i] ?? null, (float) ($recAmt[$i] ?? 0));
        }

        $siDesc = $serviceData['si_item_description'] ?? [];
        $siAmt = $serviceData['si_amount'] ?? [];
        if (!is_array($siDesc)) {
            $siDesc = [$siDesc];
        }
        if (!is_array($siAmt)) {
            $siAmt = [$siAmt];
        }
        $siRows = max(count($siDesc), count($siAmt));
        for ($i = 0; $i < $siRows; $i++) {
            $appendAmount($siDesc[$i] ?? null, (float) ($siAmt[$i] ?? 0));
        }

        foreach (($noteData['rows'] ?? []) as $row) {
            $side = strtolower((string) ($row['side'] ?? 'debit'));
            $amount = (float) ($row['amount'] ?? 0);
            $appendAmount(
                $row['particular'] ?? null,
                $side === 'credit' ? -$amount : $amount
            );
        }

        return $totals;
    }

    private function normalizeCostDescription(?string $description): ?string
    {
        $value = strtoupper(trim((string) $description));
        if ($value === '') {
            return null;
        }

        $stripped = preg_replace('/[^A-Z0-9]+/', '', $value);
        if ($stripped === null || $stripped === '') {
            return null;
        }

        $aliases = [
            'AISL' => 'AISL',
            'AISLCONTAINERCLEARANCE' => 'AISL',
            'CONTAINERCLEARANCE' => 'AISL',
            'NTC' => 'NTC',
            'CUSTOMSFORMSSTAMPS' => 'CUSTOMSFORMSSTAMPS',
            'CUSTOMSFORMS' => 'CUSTOMSFORMSSTAMPS',
            'DOCUMENTATIONPHOTOCOPY' => 'DOCUMENTATIONANDPHOTOCOPY',
            'DOCUMENTATIONANDPHOTOCOPY' => 'DOCUMENTATIONANDPHOTOCOPY',
            'NOTARIALFEEINTERCOMMERCECHARGE' => 'NOTARIALFEEANDINTERCOMMERCECHARGE',
            'NOTARIALFEEANDINTERCOMMERCECHARGE' => 'NOTARIALFEEANDINTERCOMMERCECHARGE',
            'NOTARIALSTAMP' => 'NOTARIAL',
            'HANDLINGFEE' => 'HANDLINGFEE',
            'ARRASTRECHARGE' => 'ARRASTRECHARGE',
            'ARRASTRECHARGES' => 'ARRASTRECHARGE',
            'WHARFAGEFEE' => 'WHARFAGEDUE',
            'WHARFAGEDUE' => 'WHARFAGEDUE',
            'BANKCHARGE' => 'BANKCHARGE',
            'BREAKBULKFEE' => 'BREAKBULKFEE',
            'BROKERAGEFEE' => 'BROKERAGEFEE',
            'BROKERAGEFEEASPERCAO12001' => 'BROKERAGEFEE',
            'WITHHOLDINGTAX' => 'LESSWITHHOLDINGTAX',
            'LESSWITHHOLDINGTAX' => 'LESSWITHHOLDINGTAX',
            'CFSCHARGES' => 'CFSCHARGES',
            'CHASSISRENTAL' => 'CHASSISRENTAL',
            'CLIENTSCOMMISSION' => 'CLIENTSCOMMISSION',
            'CLIENTCOMMISSION' => 'CLIENTSCOMMISSION',
            'CUSTOMSFACILITATION' => 'PROCESSINGEXPENSES',
            'DUTIESANDTAXES' => 'DUTIESANDTAXES',
            'DEMURRAGEFEE' => 'DEMURRAGEFEE',
            'DEMURRAGECHARGES' => 'DEMURRAGEFEE',
            'EXTREMEFREIGHTBILL' => 'EXTREMEFREIGHTBILL',
            'FCLCHARGES' => 'FCLCHARGESTHCBLFEEETC',
            'FCLCHARGESTHCBLFEEETC' => 'FCLCHARGESTHCBLFEEETC',
            'FREIGHTLCLCHARGESTHCBREAKBULKFEE' => 'LCLCHARGES',
            'LCLTHCBREAKBULKFEE' => 'LCLCHARGES',
            'CONTAINERDEPOSIT' => 'CONTAINERDEPOSIT',
            'HUSTLING' => 'HUSTLING',
            'LOLOSTORAGE' => 'LOLOANDSTORAGE',
            'LOLOANDSTORAGE' => 'LOLOANDSTORAGE',
            'LOLOSTORAGEFEE' => 'LOLOANDSTORAGE',
            'LOLOANDSTORAGEFEE' => 'LOLOANDSTORAGE',
            'LCLCHARGES' => 'LCLCHARGES',
            'NOTARIAL' => 'NOTARIAL',
            'PROCESSINGEXPENSES' => 'PROCESSINGEXPENSES',
            'PROCESSINGFACILITATIONEXPENSES' => 'PROCESSINGEXPENSES',
            'PROCESSINGNTC' => 'PROCESSINGNTC',
            'PROCESSINGIASAOCG' => 'PROCESSINGIASAOCG',
            'PROCESSINGIAS' => 'PROCESSINGIASAOCG',
            'PROCESSINGAOCG' => 'PROCESSINGIASAOCG',
            'PROCESSINGATRIG' => 'PROCESSINGATRIG',
            'PROCESSINGWITHDRAWAL' => 'PROCESSINGWITHDRAWAL',
            'PROCESSING' => 'PROCESSING',
            'ROYALTYFEE' => 'ROYALTYFEE',
            'STORAGEFEE' => 'STORAGEFEE',
            'SURETYBOND' => 'SURETYBOND',
            'SURETYBONDINSURANCEPREMIUM' => 'SURETYBOND',
            'TABS' => 'TABS',
            'TABSTERMINALAPPOINTMENTBOOKINGSYSTEM' => 'TABS',
            'TRUCKING' => 'TRUCKINGCHARGES',
            'TRUCKINGCHARGES' => 'TRUCKINGCHARGES',
            'TRUCKINGDELIVERYCHARGES' => 'TRUCKINGCHARGES',
            'EMPTYRETURN' => 'EMPTYRETURN',
            'RETURNOFEMPTYCONTAINERFEE' => 'EMPTYRETURN',
            'OTHERS' => 'OTHERS',
        ];

        return $aliases[$stripped] ?? $stripped;
    }

    private function extractJoNumber(?string $raw): ?string
    {
        $value = trim((string) $raw);
        if ($value === '') {
            return null;
        }

        if (preg_match('/(\d{3,})$/', $value, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function numberToWords(float $num): string
    {
        $isNegative = $num < 0;
        $num = abs($num);

        $ones = ['Zero','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
        $tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
        $scales = ['','Thousand','Million','Billion'];

        $chunkToWords = function (int $n) use ($ones, $tens): string {
            $parts = [];
            $hundred = intdiv($n, 100);
            $rest = $n % 100;
            if ($hundred > 0) {
                $parts[] = $ones[$hundred] . ' Hundred';
            }
            if ($rest > 0) {
                if ($rest < 20) {
                    $parts[] = $ones[$rest];
                } else {
                    $ten = intdiv($rest, 10);
                    $one = $rest % 10;
                    $parts[] = $tens[$ten] . ($one ? ' ' . $ones[$one] : '');
                }
            }
            return implode(' ', $parts);
        };

        if ($num == 0.0) {
            return 'ZERO PESOS ONLY';
        }

        $whole = (int) floor($num);
        $words = [];
        $scaleIndex = 0;
        while ($whole > 0) {
            $chunk = $whole % 1000;
            if ($chunk > 0) {
                $chunkWords = $chunkToWords($chunk);
                $scale = $scales[$scaleIndex] ? ' ' . $scales[$scaleIndex] : '';
                array_unshift($words, trim($chunkWords . $scale));
            }
            $whole = intdiv($whole, 1000);
            $scaleIndex++;
        }

        $cents = (int) round(($num - floor($num)) * 100);
        $centsText = $cents > 0 ? ' & ' . str_pad((string) $cents, 2, '0', STR_PAD_LEFT) . '/100' : '';

        $text = strtoupper(implode(' ', $words) . $centsText . ' PESOS ONLY');
        return $isNegative ? ('NEGATIVE ' . $text) : $text;
    }
}
