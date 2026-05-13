<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingStatement;
use App\Models\Client;
use App\Models\DebitCreditNote;
use App\Models\JobOrder;
use App\Models\ReimbursableVoucherItem;
use App\Models\ServiceInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CostSheetController extends Controller
{
    private const COST_INFO_KEYS = [
        'AISL' => 'A I S L',
        'NTC' => 'NTC',
        'CUSTOMSFORMSSTAMPS' => 'CUSTOMS FORMS & STAMPS',
        'DOCUMENTATIONANDPHOTOCOPY' => 'DOCUMENTATION AND PHOTOCOPY',
        'NOTARIALFEEANDINTERCOMMERCECHARGE' => 'NOTARIAL FEE & INTERCOMMERCE CHARGE',
        'HANDLINGFEE' => 'HANDLING FEE',
        'ARRASTRECHARGE' => 'ARRASTRE CHARGE',
        'WHARFAGEDUE' => 'WHARFAGE DUE',
        'BANKCHARGE' => 'BANK CHARGE',
        'BREAKBULKFEE' => 'BREAKBULK FEE',
        'BROKERAGEFEE' => 'BROKERAGE FEE',
        'LESSWITHHOLDINGTAX' => 'LESS WITHHOLDING TAX',
        'LESSPENALTY' => 'LESS: PENALTY',
        'CFSCHARGES' => 'CFS CHARGES',
        'CHASSISRENTAL' => 'CHASSIS RENTAL',
        'CLIENTSCOMMISSION' => 'CLIENT\'S COMMISSION',
        'CUSTOMSFACILITATION' => 'CUSTOMS FACILITATION',
        'DUTIESANDTAXES' => 'DUTIES AND TAXES',
        'DEMURRAGEFEE' => 'DEMURRAGE FEE',
        'EXTREMEFREIGHTBILL' => 'EXTREME FREIGHT BILL',
        'FCLCHARGESTHCBLFEEETC' => 'FCL CHARGES (THC, BL FEE, ETC.)',
        'CONTAINERDEPOSIT' => 'CONTAINER DEPOSIT',
        'HUSTLING' => 'HUSTLING',
        'LOLOANDSTORAGE' => 'L O L O & STORAGE',
        'LCLCHARGES' => 'LCL CHARGES',
        'NOTARIAL' => 'NOTARIAL',
        'PROCESSINGEXPENSES' => 'PROCESSING EXPENSES',
        'PROCESSINGNTC' => 'PROCESSING - NTC',
        'PROCESSINGIASAOCG' => 'PROCESSING - IAS/AOCG',
        'PROCESSINGATRIG' => 'PROCESSING - ATRIG',
        'PROCESSINGWITHDRAWAL' => 'PROCESSING - WITHDRAWAL',
        'PROCESSING' => 'PROCESSING.',
        'ROYALTYFEE' => 'ROYALTY FEE',
        'STORAGEFEE' => 'STORAGE FEE',
        'SURETYBOND' => 'SURETY BOND',
        'TABS' => 'T A B S',
        'TRUCKINGCHARGES' => 'TRUCKING CHARGES',
        'EMPTYRETURN' => 'EMPTY RETURN',
        'OTHERS' => 'OTHERS',
    ];

    public function index()
    {
        $search = trim((string) request()->query('q', ''));

        $jobOrders = JobOrder::query()
            ->select(['id', 'code', 'number', 'consignee'])
            ->get();

        $jobOrdersById = $jobOrders->keyBy('id');
        $jobOrdersByNumber = $jobOrders
            ->filter(fn (JobOrder $jobOrder) => trim((string) ($jobOrder->number ?? '')) !== '')
            ->keyBy(fn (JobOrder $jobOrder) => trim((string) $jobOrder->number));

        $billingSheets = BillingStatement::query()
            ->select(['job_order_id', 'statement_no', 'created_at'])
            ->where('document_type', '!=', 'service_invoice')
            ->whereNotNull('job_order_id')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($row) => [
                'job_order_id' => $row->job_order_id,
                'reference_no' => (string) ($row->statement_no ?? ''),
                'created_at' => $row->created_at,
                'document_source' => 'billing_statement',
            ]);

        $serviceSheets = ServiceInvoice::query()
            ->select(['job_order_id', 'statement_no', 'created_at'])
            ->whereNotNull('job_order_id')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($row) => [
                'job_order_id' => $row->job_order_id,
                'reference_no' => (string) ($row->statement_no ?? ''),
                'created_at' => $row->created_at,
                'document_source' => 'service_invoice',
            ]);

        $noteSheets = DebitCreditNote::query()
            ->select(['job_order_id', 'note_no', 'created_at'])
            ->whereNotNull('job_order_id')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($row) => [
                'job_order_id' => $row->job_order_id,
                'reference_no' => preg_replace('/^DCN-/i', '', (string) ($row->note_no ?? '')) ?: '',
                'created_at' => $row->created_at,
                'document_source' => 'debit_credit_note',
            ]);

        $voucherSheets = ReimbursableVoucherItem::query()
            ->with(['voucher:id,voucher_no,created_at'])
            ->select(['jo_no', 'reimbursable_voucher_id'])
            ->whereNotNull('jo_no')
            ->where('jo_no', '!=', '')
            ->orderByDesc('reimbursable_voucher_id')
            ->orderByDesc('id')
            ->get()
            ->map(function (ReimbursableVoucherItem $row) use ($jobOrdersByNumber) {
                $joNumber = $this->extractJoNumber((string) $row->jo_no);
                $jobOrder = $joNumber !== null ? $jobOrdersByNumber->get($joNumber) : null;
                $voucher = $row->voucher;

                if (!$jobOrder || !$voucher) {
                    return null;
                }

                return [
                    'job_order_id' => $jobOrder->id,
                    'reference_no' => (string) ($voucher->voucher_no ?? ''),
                    'created_at' => $voucher->created_at,
                    'document_source' => 'reimbursable_voucher',
                    'dedupe_key' => $jobOrder->id . '|' . $voucher->id,
                ];
            })
            ->filter()
            ->unique('dedupe_key')
            ->values();

        $availableCostSheets = $billingSheets
            ->concat($serviceSheets)
            ->concat($noteSheets)
            ->concat($voucherSheets)
            ->groupBy('job_order_id')
            ->map(function ($rows, $jobOrderId) use ($jobOrdersById) {
                $latest = collect($rows)
                    ->sortByDesc('created_at')
                    ->first();

                $jobOrder = $jobOrdersById->get($jobOrderId);
                if (!$jobOrder || empty($jobOrder->number)) {
                    return null;
                }

                $code = trim((string) ($jobOrder->code ?? ''));
                $number = trim((string) ($jobOrder->number ?? ''));
                $client = trim((string) ($jobOrder->consignee ?? ''));

                return [
                    'client' => $client,
                    'jo_number' => $number,
                    'jo_display' => $code !== '' ? "{$code} - {$number}" : $number,
                    'reference_no' => (string) ($latest['reference_no'] ?? ''),
                    'document_source' => (string) ($latest['document_source'] ?? ''),
                    'created_at' => $latest['created_at'] ?? null,
                ];
            })
            ->filter()
            ->sortBy([
                ['created_at', 'desc'],
                ['client', 'asc'],
            ])
            ->filter(function (array $sheet) use ($search) {
                if ($search === '') {
                    return true;
                }

                $haystacks = [
                    $sheet['client'] ?? '',
                    $sheet['jo_number'] ?? '',
                    $sheet['jo_display'] ?? '',
                    $sheet['reference_no'] ?? '',
                    $sheet['document_source'] ?? '',
                ];

                foreach ($haystacks as $value) {
                    if (stripos((string) $value, $search) !== false) {
                        return true;
                    }
                }

                return false;
            })
            ->values();

        $clientGroups = $availableCostSheets
            ->groupBy(function (array $sheet) {
                $client = trim((string) ($sheet['client'] ?? ''));

                return $client !== '' ? $client : 'Unassigned Client';
            })
            ->map(function ($rows, $client) {
                $rows = collect($rows)
                    ->sortBy([
                        ['created_at', 'desc'],
                        ['jo_number', 'asc'],
                    ])
                    ->values();

                return [
                    'client' => $client,
                    'sheet_count' => $rows->count(),
                    'latest_created_at' => $rows->first()['created_at'] ?? null,
                    'rows' => $rows,
                ];
            })
            ->sortBy([
                ['latest_created_at', 'desc'],
                ['client', 'asc'],
            ])
            ->values();

        return view('admin.cost-sheets.index', [
            'availableCostSheets' => $availableCostSheets,
            'clientGroups' => $clientGroups,
            'search' => $search,
        ]);
    }

    public function create(Request $request)
    {
        $jobOrders = JobOrder::query()
            ->select(['id', 'code', 'number', 'consignee'])
            ->whereNotNull('number')
            ->where('number', '!=', '')
            ->whereNotNull('consignee')
            ->where('consignee', '!=', '')
            ->orderByDesc('created_at')
            ->get();

        $clientNames = Client::query()
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->orderBy('name')
            ->pluck('name')
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->values();

        $jobOrderClientNames = $jobOrders
            ->pluck('consignee')
            ->map(fn (?string $name) => trim((string) $name))
            ->filter()
            ->unique()
            ->values();

        $allClientNames = $clientNames
            ->merge($jobOrderClientNames)
            ->unique()
            ->sort()
            ->values();

        $jobOrdersForSelect = $jobOrders
            ->map(function (JobOrder $jobOrder) {
                $code = trim((string) ($jobOrder->code ?? ''));
                $number = trim((string) ($jobOrder->number ?? ''));

                return [
                    'id' => $jobOrder->id,
                    'consignee' => trim((string) ($jobOrder->consignee ?? '')),
                    'code' => $code,
                    'number' => $number,
                    'jo_display' => $code !== '' ? ($code . ' - ' . $number) : $number,
                ];
            })
            ->values();

        $jobOrderIdsByNumber = $jobOrders
            ->filter(fn (JobOrder $jobOrder) => !empty($jobOrder->number))
            ->mapWithKeys(fn (JobOrder $jobOrder) => [trim((string) $jobOrder->number) => $jobOrder->id]);

        $jobOrderIds = $jobOrderIdsByNumber->values()->unique()->all();

        $billingLatestByJobOrderId = BillingStatement::query()
            ->select(['job_order_id', 'statement_no', 'data', 'created_at'])
            ->whereIn('job_order_id', $jobOrderIds)
            ->where('document_type', '!=', 'service_invoice')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('job_order_id');

        $serviceLatestByJobOrderId = ServiceInvoice::query()
            ->select(['job_order_id', 'statement_no', 'data', 'created_at'])
            ->whereIn('job_order_id', $jobOrderIds)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('job_order_id');

        $noteLatestByJobOrderId = DebitCreditNote::query()
            ->select(['job_order_id', 'note_no', 'amount', 'data', 'created_at'])
            ->whereIn('job_order_id', $jobOrderIds)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('job_order_id');

        $docRefsByJoNumber = [];
        $docAmountsByJoNumber = [];
        $docVatByJoNumber = [];
        $docWithholdingByJoNumber = [];
        $docNoteIncludedByJoNumber = [];
        $docAdvanceAmountsByJoNumber = [];
        $docEntriesByJoNumber = [];
        $costDocAmountsByJoNumber = [];
        $costAtAmountsByJoNumber = [];
        $costAtCvNosByJoNumber = [];
        $costAtRemarksByJoNumber = [];
        $costDocOtherItemsByJoNumber = [];
        $costAtOtherItemsByJoNumber = [];
        $costAtOtherCvNosByJoNumber = [];
        $defaultCostSheetDate = null;
        $defaultCostSheetDateDisplay = null;
        $selectedJoNumber = trim((string) $request->query('jo'));
        $selectedJobOrderId = $selectedJoNumber !== '' ? $jobOrderIdsByNumber->get($selectedJoNumber) : null;

        if ($selectedJobOrderId) {
            $selectedVoucherDates = ReimbursableVoucherItem::query()
                ->with('voucher:id,created_at')
                ->select(['jo_no', 'reimbursable_voucher_id'])
                ->whereNotNull('jo_no')
                ->where('jo_no', '!=', '')
                ->get()
                ->filter(fn (ReimbursableVoucherItem $item) => $this->extractJoNumber((string) $item->jo_no) === $selectedJoNumber)
                ->map(fn (ReimbursableVoucherItem $item) => $item->voucher?->created_at)
                ->filter();

            $earliestDocumentDate = collect([
                BillingStatement::query()
                    ->where('job_order_id', $selectedJobOrderId)
                    ->where('document_type', '!=', 'service_invoice')
                    ->min('created_at'),
                ServiceInvoice::query()
                    ->where('job_order_id', $selectedJobOrderId)
                    ->min('created_at'),
                DebitCreditNote::query()
                    ->where('job_order_id', $selectedJobOrderId)
                    ->min('created_at'),
                $selectedVoucherDates->min(),
            ])
                ->filter()
                ->map(fn ($value) => Carbon::parse($value))
                ->sort()
                ->first();

            if ($earliestDocumentDate) {
                $defaultCostSheetDate = $earliestDocumentDate->format('Y-m-d');
                $defaultCostSheetDateDisplay = $earliestDocumentDate->format('d/m/Y');
            }
        }

        $voucherItemsByJoNumber = [];
        $voucherOtherItemsByJoNumber = [];
        ReimbursableVoucherItem::query()
            ->with('voucher:id,voucher_no')
            ->select(['jo_no', 'description', 'amount', 'rv_cv_no', 'remarks', 'payee', 'reimbursable_voucher_id', 'deduction_type'])
            ->whereNotNull('jo_no')
            ->where('jo_no', '!=', '')
            ->orderBy('id')
            ->get()
            ->each(function (ReimbursableVoucherItem $item) use (&$voucherItemsByJoNumber, &$voucherOtherItemsByJoNumber, &$costAtCvNosByJoNumber, &$costAtRemarksByJoNumber, &$costAtOtherCvNosByJoNumber): void {
                $joNumber = $this->extractJoNumber((string) $item->jo_no);
                if ($joNumber === null) {
                    return;
                }

                if ($this->shouldIgnoreCostDescription($item->description)) {
                    return;
                }

                $deductionType = $this->resolveVoucherDeductionType($item);
                if ($deductionType === 'advance') {
                    return;
                }

                $signedAmount = (float) ($item->amount ?? 0);
                if ($deductionType === 'penalty') {
                    $signedAmount = -abs($signedAmount);
                }

                $key = $this->normalizeCostDescription($item->description);
                if ($key === null) {
                    $this->appendOtherCostLine(
                        $voucherOtherItemsByJoNumber[$joNumber],
                        $item->description,
                        $signedAmount,
                        $item->remarks
                    );
                    $this->appendCvNumber(
                        $costAtOtherCvNosByJoNumber[$joNumber],
                        trim((string) $item->description),
                        $item->rv_cv_no ?: $item->voucher?->voucher_no
                    );
                    return;
                }

                $voucherItemsByJoNumber[$joNumber][$key] = round(
                    (float) (($voucherItemsByJoNumber[$joNumber][$key] ?? 0) + $signedAmount),
                    2
                );
                $this->appendCvNumber(
                    $costAtCvNosByJoNumber[$joNumber],
                    $key,
                    $item->rv_cv_no ?: $item->voucher?->voucher_no
                );
                $this->appendRemark(
                    $costAtRemarksByJoNumber[$joNumber],
                    $key,
                    $this->costSheetRemarkFromVoucherItem($item)
                );
            });

        foreach ($jobOrderIdsByNumber as $joNumber => $jobOrderId) {
            $billingDocuments = collect($billingLatestByJobOrderId->get($jobOrderId) ?? [])->sortBy([
                ['created_at', 'asc'],
                ['id', 'asc'],
            ])->values();
            $serviceDocuments = collect($serviceLatestByJobOrderId->get($jobOrderId) ?? [])->sortBy([
                ['created_at', 'asc'],
                ['id', 'asc'],
            ])->values();
            $noteDocuments = collect($noteLatestByJobOrderId->get($jobOrderId) ?? [])->sortBy([
                ['created_at', 'asc'],
                ['id', 'asc'],
            ])->values();

            $latestBilling = $billingDocuments->last();
            $latestService = $serviceDocuments->last();
            $latestNote = $noteDocuments->last();
            $billingData = is_array($latestBilling?->data) ? $latestBilling->data : [];
            $serviceData = is_array($latestService?->data) ? $latestService->data : [];
            $noteData = is_array($latestNote?->data) ? $latestNote->data : [];

            $docRefsByJoNumber[$joNumber] = [
                'billing_statement' => (string) ($latestBilling?->statement_no ?? ''),
                'service_invoice' => (string) ($latestService?->statement_no ?? ''),
                'debit_credit_note' => preg_replace('/^DCN-/i', '', (string) ($latestNote?->note_no ?? '')) ?: '',
            ];

            $docAmountsByJoNumber[$joNumber] = [
                'billing_statement' => is_numeric($billingData['grand_total'] ?? null)
                    ? (float) $billingData['grand_total']
                    : 0.0,
                'service_invoice' => is_numeric($serviceData['si_amount_net_vat'] ?? null)
                    ? (float) $serviceData['si_amount_net_vat']
                    : (is_numeric($serviceData['si_total_amount_due'] ?? null)
                        ? (float) $serviceData['si_total_amount_due']
                        : (is_numeric($serviceData['grand_total'] ?? null) ? (float) $serviceData['grand_total'] : 0.0)),
                'debit_credit_note' => is_numeric($latestNote?->amount ?? null)
                    ? (float) $latestNote->amount
                    : (is_numeric($noteData['net_total'] ?? null) ? (float) $noteData['net_total'] : 0.0),
            ];

            $docVatByJoNumber[$joNumber] = [
                'service_invoice' => is_numeric($serviceData['si_less_vat'] ?? null)
                    ? (float) $serviceData['si_less_vat']
                    : (is_numeric($serviceData['si_add_vat'] ?? null)
                        ? (float) $serviceData['si_add_vat']
                        : (is_numeric($serviceData['si_vat'] ?? null) ? (float) $serviceData['si_vat'] : 0.0)),
            ];

            $docWithholdingByJoNumber[$joNumber] = [
                'service_invoice' => is_numeric($serviceData['si_less_withholding_tax'] ?? null)
                    ? (float) $serviceData['si_less_withholding_tax']
                    : 0.0,
            ];

            $docNoteIncludedByJoNumber[$joNumber] = !array_key_exists('include_in_cost_sheet', $noteData)
                || (bool) ($noteData['include_in_cost_sheet'] ?? false);
            $docAdvanceAmountsByJoNumber[$joNumber] = 0.0;
            $docEntriesByJoNumber[$joNumber] = [];

            foreach ($billingDocuments as $document) {
                $data = is_array($document->data) ? $document->data : [];
                $amount = is_numeric($data['grand_total'] ?? null)
                    ? (float) $data['grand_total']
                    : 0.0;

                if ((string) ($document->statement_no ?? '') !== '' || abs($amount) > 0.00001) {
                    $docEntriesByJoNumber[$joNumber][] = [
                        'type' => 'billing_statement',
                        'label' => 'BILLING STATEMENT',
                        'ref' => (string) ($document->statement_no ?? ''),
                        'amount' => round($amount, 2),
                        'include_in_sales' => true,
                    ];
                }
            }

            foreach ($serviceDocuments as $document) {
                $data = is_array($document->data) ? $document->data : [];
                $amount = is_numeric($data['si_amount_net_vat'] ?? null)
                    ? (float) $data['si_amount_net_vat']
                    : (is_numeric($data['si_total_amount_due'] ?? null)
                        ? (float) $data['si_total_amount_due']
                        : (is_numeric($data['grand_total'] ?? null) ? (float) $data['grand_total'] : 0.0));

                if ((string) ($document->statement_no ?? '') !== '' || abs($amount) > 0.00001) {
                    $docEntriesByJoNumber[$joNumber][] = [
                        'type' => 'service_invoice',
                        'label' => 'SERVICE INVOICE',
                        'ref' => (string) ($document->statement_no ?? ''),
                        'amount' => round($amount, 2),
                        'include_in_sales' => true,
                    ];
                }
            }

            foreach ($noteDocuments as $document) {
                $data = is_array($document->data) ? $document->data : [];
                $includeInSales = !array_key_exists('include_in_cost_sheet', $data)
                    || (bool) ($data['include_in_cost_sheet'] ?? false);
                $amount = is_numeric($document->amount ?? null)
                    ? (float) $document->amount
                    : (is_numeric($data['net_total'] ?? null) ? (float) $data['net_total'] : 0.0);

                if ((string) ($document->note_no ?? '') !== '' || abs($amount) > 0.00001) {
                    $docEntriesByJoNumber[$joNumber][] = [
                        'type' => 'debit_credit_note',
                        'label' => $amount < 0 ? 'CREDIT NOTE' : 'DEBIT NOTE',
                        'ref' => preg_replace('/^DCN-/i', '', (string) ($document->note_no ?? '')) ?: '',
                        'amount' => round(abs($amount), 2),
                        'sales_sign' => $amount < 0 ? -1 : 1,
                        'include_in_sales' => $includeInSales,
                    ];
                }

                $docAdvanceAmountsByJoNumber[$joNumber] += $this->extractAdvanceAmount($data);
            }

            $costInfoData = [
                'totals' => [],
                'others' => [],
            ];
            foreach ($billingDocuments as $document) {
                $costInfoData = $this->mergeCostInfoData(
                    $costInfoData,
                    $this->buildCostInfoData(is_array($document->data) ? $document->data : [], [], [])
                );
            }
            foreach ($serviceDocuments as $document) {
                $costInfoData = $this->mergeCostInfoData(
                    $costInfoData,
                    $this->buildCostInfoData([], is_array($document->data) ? $document->data : [], [])
                );
            }
            foreach ($noteDocuments as $document) {
                $costInfoData = $this->mergeCostInfoData(
                    $costInfoData,
                    $this->buildCostInfoData([], [], is_array($document->data) ? $document->data : [])
                );
            }
            $costDocAmountsByJoNumber[$joNumber] = $costInfoData['totals'];
            $costDocOtherItemsByJoNumber[$joNumber] = $costInfoData['others'];

            $costAtAmountsByJoNumber[$joNumber] = $voucherItemsByJoNumber[$joNumber] ?? [];
            $costAtCvNosByJoNumber[$joNumber] = $costAtCvNosByJoNumber[$joNumber] ?? [];
            $costAtRemarksByJoNumber[$joNumber] = $costAtRemarksByJoNumber[$joNumber] ?? [];
            $costAtOtherItemsByJoNumber[$joNumber] = array_values($voucherOtherItemsByJoNumber[$joNumber] ?? []);
            $costAtOtherCvNosByJoNumber[$joNumber] = $costAtOtherCvNosByJoNumber[$joNumber] ?? [];
        }

        return view('admin.cost-sheets.create', [
            'clients' => $allClientNames,
            'jobOrdersForSelect' => $jobOrdersForSelect,
            'docRefsByJoNumber' => $docRefsByJoNumber,
            'docAmountsByJoNumber' => $docAmountsByJoNumber,
            'docVatByJoNumber' => $docVatByJoNumber,
            'docWithholdingByJoNumber' => $docWithholdingByJoNumber,
            'docNoteIncludedByJoNumber' => $docNoteIncludedByJoNumber,
            'docAdvanceAmountsByJoNumber' => $docAdvanceAmountsByJoNumber,
            'docEntriesByJoNumber' => $docEntriesByJoNumber,
            'costDocAmountsByJoNumber' => $costDocAmountsByJoNumber,
            'costAtAmountsByJoNumber' => $costAtAmountsByJoNumber,
            'costAtCvNosByJoNumber' => $costAtCvNosByJoNumber,
            'costAtRemarksByJoNumber' => $costAtRemarksByJoNumber,
            'costDocOtherItemsByJoNumber' => $costDocOtherItemsByJoNumber,
            'costAtOtherItemsByJoNumber' => $costAtOtherItemsByJoNumber,
            'costAtOtherCvNosByJoNumber' => $costAtOtherCvNosByJoNumber,
            'defaultClientName' => trim((string) $request->query('client')),
            'defaultJoNumber' => trim((string) $request->query('jo')),
            'defaultCostSheetDate' => $defaultCostSheetDate,
            'defaultCostSheetDateDisplay' => $defaultCostSheetDateDisplay,
        ]);
    }

    public function salesReport(Request $request)
    {
        $period = in_array($request->query('period'), ['week', 'month', 'year'], true)
            ? $request->query('period')
            : 'month';

        $anchorDate = $request->filled('date')
            ? Carbon::parse((string) $request->query('date'))
            : now();

        [$startDate, $endDate, $periodLabel] = match ($period) {
            'week' => [
                $anchorDate->copy()->startOfWeek(),
                $anchorDate->copy()->endOfWeek(),
                $anchorDate->copy()->startOfWeek()->format('M d, Y') . ' - ' . $anchorDate->copy()->endOfWeek()->format('M d, Y'),
            ],
            'year' => [
                $anchorDate->copy()->startOfYear(),
                $anchorDate->copy()->endOfYear(),
                $anchorDate->format('Y'),
            ],
            default => [
                $anchorDate->copy()->startOfMonth(),
                $anchorDate->copy()->endOfMonth(),
                $anchorDate->format('F Y'),
            ],
        };

        $rows = $this->buildSalesReportRows()
            ->filter(function (array $row) use ($startDate, $endDate) {
                $periodDate = $row['period_date'] ?? null;

                return $periodDate instanceof Carbon
                    && $periodDate->betweenIncluded($startDate, $endDate);
            })
            ->sortByDesc('period_date')
            ->values();

        $totals = [
            'sales_total' => round((float) $rows->sum('sales_total'), 2),
            'billed_total' => round((float) $rows->sum('billed_total'), 2),
            'at_cost_total' => round((float) $rows->sum('at_cost_total'), 2),
            'gross_income' => round((float) $rows->sum('gross_income'), 2),
        ];

        return view('admin.cost-sheets.sales-report', [
            'rows' => $rows,
            'period' => $period,
            'anchorDate' => $anchorDate->format('Y-m-d'),
            'periodLabel' => $periodLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totals' => $totals,
        ]);
    }

    public function serviceInvoiceSummary(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $serviceInvoices = ServiceInvoice::query()
            ->with(['jobOrder:id,code,number,consignee'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($search !== '') {
            $serviceInvoices->where(function ($query) use ($search) {
                $query->where('statement_no', 'like', '%' . $search . '%')
                    ->orWhereHas('jobOrder', function ($jobOrderQuery) use ($search) {
                        $jobOrderQuery->where('consignee', 'like', '%' . $search . '%')
                            ->orWhere('number', 'like', '%' . $search . '%')
                            ->orWhere('code', 'like', '%' . $search . '%');
                    });
            });
        }

        $rows = $serviceInvoices->get()->map(function (ServiceInvoice $serviceInvoice) {
            $data = is_array($serviceInvoice->data) ? $serviceInvoice->data : [];
            $jobOrder = $serviceInvoice->jobOrder;
            $joNumber = trim((string) ($jobOrder?->number ?? ''));
            $joCode = trim((string) ($jobOrder?->code ?? ''));
            $vatAmount = is_numeric($data['si_less_vat'] ?? null)
                ? (float) $data['si_less_vat']
                : (is_numeric($data['si_vat'] ?? null) ? (float) $data['si_vat'] : 0.0);

            return [
                'id' => $serviceInvoice->id,
                'statement_no' => (string) ($serviceInvoice->statement_no ?? ''),
                'client' => trim((string) ($jobOrder?->consignee ?? '')),
                'jo_display' => $joCode !== '' && $joNumber !== '' ? ($joCode . ' - ' . $joNumber) : ($joNumber !== '' ? $joNumber : '—'),
                'total_sales' => (float) ($data['si_total_sales'] ?? 0),
                'less_vat' => $vatAmount,
                'amount_net_vat' => (float) ($data['si_amount_net_vat'] ?? 0),
                'less_withholding_tax' => (float) ($data['si_less_withholding_tax'] ?? 0),
                'add_vat' => $vatAmount,
                'total_amount_due' => (float) ($data['si_total_amount_due'] ?? 0),
                'created_at' => $serviceInvoice->created_at,
            ];
        });

        $clientGroups = $rows
            ->groupBy(fn (array $row) => $row['client'] !== '' ? $row['client'] : 'Unassigned Client')
            ->map(function ($group, $clientName) {
                $sortedRows = collect($group)
                    ->sortByDesc('created_at')
                    ->values();

                return [
                    'client' => $clientName,
                    'si_count' => $sortedRows->count(),
                    'total_sales' => round((float) $sortedRows->sum('total_sales'), 2),
                    'less_vat' => round((float) $sortedRows->sum('less_vat'), 2),
                    'amount_net_vat' => round((float) $sortedRows->sum('amount_net_vat'), 2),
                    'less_withholding_tax' => round((float) $sortedRows->sum('less_withholding_tax'), 2),
                    'add_vat' => round((float) $sortedRows->sum('add_vat'), 2),
                    'total_amount_due' => round((float) $sortedRows->sum('total_amount_due'), 2),
                    'rows' => $sortedRows,
                ];
            })
            ->sortBy('client', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $totals = [
            'si_count' => $rows->count(),
            'total_sales' => round((float) $rows->sum('total_sales'), 2),
            'less_vat' => round((float) $rows->sum('less_vat'), 2),
            'amount_net_vat' => round((float) $rows->sum('amount_net_vat'), 2),
            'less_withholding_tax' => round((float) $rows->sum('less_withholding_tax'), 2),
            'add_vat' => round((float) $rows->sum('add_vat'), 2),
            'total_amount_due' => round((float) $rows->sum('total_amount_due'), 2),
        ];

        return view('admin.cost-sheets.service-invoice-summary', [
            'clientGroups' => $clientGroups,
            'search' => $search,
            'totals' => $totals,
        ]);
    }

    private function buildSalesReportRows()
    {
        $jobOrders = JobOrder::query()
            ->select(['id', 'code', 'number', 'consignee'])
            ->whereNotNull('number')
            ->where('number', '!=', '')
            ->get()
            ;

        $jobOrdersById = $jobOrders->keyBy('id');
        $jobOrdersByNumber = $jobOrders->keyBy(fn (JobOrder $jobOrder) => trim((string) $jobOrder->number));

        $billingLatestByJobOrderId = BillingStatement::query()
            ->select(['job_order_id', 'statement_no', 'data', 'created_at'])
            ->whereNotNull('job_order_id')
            ->where('document_type', '!=', 'service_invoice')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('job_order_id');

        $serviceLatestByJobOrderId = ServiceInvoice::query()
            ->select(['job_order_id', 'statement_no', 'data', 'created_at'])
            ->whereNotNull('job_order_id')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('job_order_id');

        $noteLatestByJobOrderId = DebitCreditNote::query()
            ->select(['job_order_id', 'note_no', 'amount', 'data', 'created_at'])
            ->whereNotNull('job_order_id')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('job_order_id');

        $voucherItemsByJoNumber = [];
        $voucherOtherItemsByJoNumber = [];
        $voucherLatestDateByJoNumber = [];
        ReimbursableVoucherItem::query()
            ->with('voucher:id,created_at')
            ->select(['jo_no', 'description', 'amount', 'reimbursable_voucher_id', 'deduction_type'])
            ->whereNotNull('jo_no')
            ->where('jo_no', '!=', '')
            ->orderBy('id')
            ->get()
            ->each(function (ReimbursableVoucherItem $item) use (&$voucherItemsByJoNumber, &$voucherOtherItemsByJoNumber, &$voucherLatestDateByJoNumber): void {
                $joNumber = $this->extractJoNumber((string) $item->jo_no);
                if ($joNumber === null) {
                    return;
                }

                $voucherCreatedAt = $item->voucher?->created_at;
                if (
                    $voucherCreatedAt
                    && (
                        !isset($voucherLatestDateByJoNumber[$joNumber])
                        || Carbon::parse($voucherCreatedAt)->gt(Carbon::parse($voucherLatestDateByJoNumber[$joNumber]))
                    )
                ) {
                    $voucherLatestDateByJoNumber[$joNumber] = $voucherCreatedAt;
                }

                if ($this->shouldIgnoreCostDescription($item->description)) {
                    return;
                }

                $deductionType = $this->resolveVoucherDeductionType($item);
                if ($deductionType === 'advance') {
                    return;
                }

                $signedAmount = (float) ($item->amount ?? 0);
                if ($deductionType === 'penalty') {
                    $signedAmount = -abs($signedAmount);
                }

                $key = $this->normalizeCostDescription($item->description);
                if ($key === null) {
                    $this->appendOtherCostLine(
                        $voucherOtherItemsByJoNumber[$joNumber],
                        $item->description,
                        $signedAmount
                    );
                    return;
                }

                $voucherItemsByJoNumber[$joNumber][$key] = round(
                    (float) (($voucherItemsByJoNumber[$joNumber][$key] ?? 0) + $signedAmount),
                    2
                );
            });

        $jobOrderIds = collect()
            ->merge($billingLatestByJobOrderId->keys())
            ->merge($serviceLatestByJobOrderId->keys())
            ->merge($noteLatestByJobOrderId->keys())
            ->merge(
                collect(array_keys($voucherItemsByJoNumber))
                    ->merge(array_keys($voucherOtherItemsByJoNumber))
                    ->unique()
                    ->map(fn (string $joNumber) => $jobOrdersByNumber->get($joNumber)?->id)
                    ->filter()
            )
            ->unique()
            ->values();

        return $jobOrderIds->map(function ($jobOrderId) use (
            $jobOrdersById,
            $billingLatestByJobOrderId,
            $serviceLatestByJobOrderId,
            $noteLatestByJobOrderId,
            $voucherItemsByJoNumber,
            $voucherOtherItemsByJoNumber
        ) {
            $jobOrder = $jobOrdersById->get($jobOrderId);
            if (!$jobOrder) {
                return null;
            }

            $billingDocuments = collect($billingLatestByJobOrderId->get($jobOrderId) ?? []);
            $serviceDocuments = collect($serviceLatestByJobOrderId->get($jobOrderId) ?? []);
            $noteDocuments = collect($noteLatestByJobOrderId->get($jobOrderId) ?? []);

            $salesTotal = 0.0;
            foreach ($billingDocuments as $document) {
                $data = is_array($document->data) ? $document->data : [];
                if (is_numeric($data['grand_total'] ?? null)) {
                    $salesTotal += (float) $data['grand_total'];
                }
            }
            foreach ($serviceDocuments as $document) {
                $data = is_array($document->data) ? $document->data : [];
                if (is_numeric($data['si_amount_net_vat'] ?? null)) {
                    $salesTotal += (float) $data['si_amount_net_vat'];
                } elseif (is_numeric($data['si_total_amount_due'] ?? null)) {
                    $salesTotal += (float) $data['si_total_amount_due'];
                } elseif (is_numeric($data['grand_total'] ?? null)) {
                    $salesTotal += (float) $data['grand_total'];
                }
            }
            foreach ($noteDocuments as $document) {
                $data = is_array($document->data) ? $document->data : [];
                $includeInSales = !array_key_exists('include_in_cost_sheet', $data)
                    || (bool) ($data['include_in_cost_sheet'] ?? false);
                if (!$includeInSales) {
                    continue;
                }

                if (is_numeric($document->amount ?? null)) {
                    $salesTotal += (float) $document->amount;
                } elseif (is_numeric($data['net_total'] ?? null)) {
                    $salesTotal += (float) $data['net_total'];
                }
            }

            $costInfoData = [
                'totals' => [],
                'others' => [],
            ];
            foreach ($billingDocuments as $document) {
                $costInfoData = $this->mergeCostInfoData(
                    $costInfoData,
                    $this->buildCostInfoData(is_array($document->data) ? $document->data : [], [], [])
                );
            }
            foreach ($serviceDocuments as $document) {
                $costInfoData = $this->mergeCostInfoData(
                    $costInfoData,
                    $this->buildCostInfoData([], is_array($document->data) ? $document->data : [], [])
                );
            }
            foreach ($noteDocuments as $document) {
                $costInfoData = $this->mergeCostInfoData(
                    $costInfoData,
                    $this->buildCostInfoData([], [], is_array($document->data) ? $document->data : [])
                );
            }
            $billedTotal = collect($costInfoData['totals'] ?? [])->sum()
                + collect($costInfoData['others'] ?? [])->sum('amount');

            $joNumber = trim((string) ($jobOrder->number ?? ''));
            $atCostTotal = collect($voucherItemsByJoNumber[$joNumber] ?? [])->sum()
                + collect($voucherOtherItemsByJoNumber[$joNumber] ?? [])->sum('amount');

            $periodDate = collect([
                $billingDocuments->max('created_at'),
                $serviceDocuments->max('created_at'),
                $noteDocuments->max('created_at'),
                $voucherLatestDateByJoNumber[$joNumber] ?? null,
            ])->filter()->sortDesc()->first();

            $code = trim((string) ($jobOrder->code ?? ''));

            return [
                'client' => trim((string) ($jobOrder->consignee ?? '')),
                'jo_number' => $joNumber,
                'jo_display' => $code !== '' ? ($code . ' - ' . $joNumber) : $joNumber,
                'period_date' => $periodDate ? Carbon::parse($periodDate) : null,
                'sales_total' => round((float) $salesTotal, 2),
                'billed_total' => round((float) $billedTotal, 2),
                'at_cost_total' => round((float) $atCostTotal, 2),
                'gross_income' => round((float) $billedTotal - (float) $atCostTotal, 2),
            ];
        })->filter()->values();
    }

    private function buildCostInfoData(array $billingData, array $serviceData, array $noteData): array
    {
        $totals = [];
        $others = [];

        $appendAmount = function (?string $description, float $amount) use (&$totals, &$others): void {
            if ($this->shouldIgnoreCostDescription($description)) {
                return;
            }

            $key = $this->normalizeCostDescription($description);
            if (abs($amount) < 0.00001) {
                return;
            }

            if ($key === null) {
                $this->appendOtherCostLine($others, $description, $amount);
                return;
            }

            $totals[$key] = ($totals[$key] ?? 0.0) + $amount;
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
        $serviceInvoiceNetOfVat = is_numeric($serviceData['si_amount_net_vat'] ?? null)
            ? (float) $serviceData['si_amount_net_vat']
            : (is_numeric($serviceData['si_total_amount_due'] ?? null)
                ? (float) $serviceData['si_total_amount_due']
                : null);
        $serviceBrokerageApplied = false;
        if (!is_array($siDesc)) {
            $siDesc = [$siDesc];
        }
        if (!is_array($siAmt)) {
            $siAmt = [$siAmt];
        }
        $siRows = max(count($siDesc), count($siAmt));
        for ($i = 0; $i < $siRows; $i++) {
            $description = $siDesc[$i] ?? null;
            $normalizedKey = $this->normalizeCostDescription($description);
            $amount = (float) ($siAmt[$i] ?? 0);

            if (
                $normalizedKey === 'BROKERAGEFEE' &&
                $serviceInvoiceNetOfVat !== null &&
                !$serviceBrokerageApplied
            ) {
                $amount = $serviceInvoiceNetOfVat;
                $serviceBrokerageApplied = true;
            }

            $appendAmount($description, $amount);
        }

        $includeDebitCreditNote = !array_key_exists('include_in_cost_sheet', $noteData)
            || (bool) ($noteData['include_in_cost_sheet'] ?? false);

        if ($includeDebitCreditNote) {
            foreach (($noteData['rows'] ?? []) as $row) {
                if ($this->isAdvanceDescription($row['particular'] ?? null)) {
                    continue;
                }

                $side = strtolower((string) ($row['side'] ?? 'debit'));
                $amount = (float) ($row['amount'] ?? 0);
                $appendAmount(
                    $row['particular'] ?? null,
                    $side === 'credit' ? -$amount : $amount
                );
            }
        }

        return [
            'totals' => collect($totals)
                ->map(fn ($amount) => round((float) $amount, 2))
                ->all(),
            'others' => array_values($others),
        ];
    }

    private function extractAdvanceAmount(array $noteData): float
    {
        $total = 0.0;

        foreach (($noteData['rows'] ?? []) as $row) {
            if (!$this->isAdvanceDescription($row['particular'] ?? null)) {
                continue;
            }

            $total += abs((float) ($row['amount'] ?? 0));
        }

        return round($total, 2);
    }

    private function mergeCostInfoData(array $base, array $incoming): array
    {
        $totals = $base['totals'] ?? [];
        foreach (($incoming['totals'] ?? []) as $key => $amount) {
            $totals[$key] = round((float) (($totals[$key] ?? 0) + (float) $amount), 2);
        }

        $others = [];
        foreach (array_merge($base['others'] ?? [], $incoming['others'] ?? []) as $item) {
            $description = trim((string) ($item['description'] ?? 'OTHERS'));
            if ($description === '') {
                $description = 'OTHERS';
            }

            $index = strtoupper($description);
            if (!isset($others[$index])) {
                $others[$index] = [
                    'description' => $description,
                    'amount' => 0.0,
                ];
            }

            $others[$index]['amount'] = round(
                (float) $others[$index]['amount'] + (float) ($item['amount'] ?? 0),
                2
            );
        }

        return [
            'totals' => $totals,
            'others' => array_values($others),
        ];
    }

    private function isAdvanceDescription(?string $description): bool
    {
        $value = strtoupper(trim((string) $description));
        if ($value === '') {
            return false;
        }

        $stripped = preg_replace('/[^A-Z0-9]+/', '', $value);
        if ($stripped === null || $stripped === '') {
            return false;
        }

        return str_contains($stripped, 'ADVANCE');
    }

    private function appendOtherCostLine(?array &$others, ?string $description, float $amount, ?string $remarks = null): void
    {
        if (abs($amount) < 0.00001) {
            return;
        }

        if (!is_array($others)) {
            $others = [];
        }

        $label = trim((string) $description);
        if ($label === '') {
            $label = 'OTHERS';
        }

        $index = strtoupper($label);

        if (!isset($others[$index])) {
            $others[$index] = [
                'description' => $label,
                'amount' => 0.0,
                'remarks' => '',
            ];
        }

        $others[$index]['amount'] = round((float) $others[$index]['amount'] + $amount, 2);
        $remarks = trim((string) $remarks);
        if ($remarks !== '') {
            $existing = collect(explode(', ', (string) ($others[$index]['remarks'] ?? '')))
                ->filter(fn ($item) => trim((string) $item) !== '')
                ->map(fn ($item) => trim((string) $item))
                ->values();

            if (!$existing->contains($remarks)) {
                $existing->push($remarks);
            }

            $others[$index]['remarks'] = $existing->implode(', ');
        }
    }

    private function appendCvNumber(?array &$map, string $key, ?string $cvNo): void
    {
        $normalizedKey = strtoupper(trim($key));
        $value = trim((string) $cvNo);

        if ($normalizedKey === '' || $value === '') {
            return;
        }

        if (!is_array($map)) {
            $map = [];
        }

        $existing = collect(explode(', ', (string) ($map[$normalizedKey] ?? '')))
            ->filter(fn ($item) => trim((string) $item) !== '')
            ->map(fn ($item) => trim((string) $item))
            ->values();

        if (!$existing->contains($value)) {
            $existing->push($value);
        }

        $map[$normalizedKey] = $existing->implode(', ');
    }

    private function appendRemark(?array &$map, string $key, ?string $remarks): void
    {
        $normalizedKey = strtoupper(trim($key));
        $value = trim((string) $remarks);

        if ($normalizedKey === '' || $value === '') {
            return;
        }

        if (!is_array($map)) {
            $map = [];
        }

        $existing = collect(explode(', ', (string) ($map[$normalizedKey] ?? '')))
            ->filter(fn ($item) => trim((string) $item) !== '')
            ->map(fn ($item) => trim((string) $item))
            ->values();

        if (!$existing->contains($value)) {
            $existing->push($value);
        }

        $map[$normalizedKey] = $existing->implode(', ');
    }

    private function costSheetRemarkFromVoucherItem(ReimbursableVoucherItem $item): ?string
    {
        $remarks = trim((string) ($item->remarks ?? ''));
        $payeeRaw = trim((string) ($item->payee ?? ''));
        if ($payeeRaw !== '') {
            $payeeTag = strtoupper($payeeRaw);
            return $remarks !== '' ? $remarks . ', REQUESTED BY: ' . $payeeTag : 'REQUESTED BY: ' . $payeeTag;
        }

        return $remarks !== '' ? $remarks : null;
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
            'CTNREQUIPMENTCLEARANCE' => 'AISL',
            'CTNREQUIPMENTCLEARANCEASLOR' => 'AISL',
            'CONTAINEREQUIPMENTCLEARANCE' => 'AISL',
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
            'LESSPENALTY' => 'LESSPENALTY',
            'CFSCHARGES' => 'CFSCHARGES',
            'CHASSISRENTAL' => 'CHASSISRENTAL',
            'CLIENTSCOMMISSION' => 'CLIENTSCOMMISSION',
            'CLIENTCOMMISSION' => 'CLIENTSCOMMISSION',
            'CUSTOMSFACILITATION' => 'CUSTOMSFACILITATION',
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
            'NOTARY' => 'NOTARIAL',
            'PROCESSINGEXPENSES' => 'PROCESSINGEXPENSES',
            'PROCESSINGFACILITATIONEXPENSES' => 'PROCESSINGEXPENSES',
            'CUSTOMSFACILITATION' => 'PROCESSINGEXPENSES',
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

        if (isset($aliases[$stripped])) {
            return $aliases[$stripped];
        }

        if (array_key_exists($stripped, self::COST_INFO_KEYS)) {
            return $stripped;
        }

        // Some source rows are combined labels; bucket them into the closest
        // existing cost-sheet line instead of pushing them to OTHERS.
        if (str_contains($stripped, 'CUSTOMSFORMS')) {
            return 'CUSTOMSFORMSSTAMPS';
        }

        if (str_contains($stripped, 'DOCUMENTATION')) {
            return 'DOCUMENTATIONANDPHOTOCOPY';
        }

        if (str_contains($stripped, 'CLEARANCE')) {
            return 'AISL';
        }

        if (str_contains($stripped, 'DEMURRAGE')) {
            return 'DEMURRAGEFEE';
        }

        if (str_contains($stripped, 'FCLCHARGES')) {
            return 'FCLCHARGESTHCBLFEEETC';
        }

        if (str_contains($stripped, 'NOTARY')) {
            return 'NOTARIAL';
        }

        if (str_contains($stripped, 'WHARFAGE')) {
            return 'WHARFAGEDUE';
        }

        if (str_contains($stripped, 'ARRASTRE')) {
            return 'ARRASTRECHARGE';
        }

        if (str_contains($stripped, 'LOLO')) {
            return 'LOLOANDSTORAGE';
        }

        if (str_contains($stripped, 'STORAGEFEE')) {
            return 'STORAGEFEE';
        }

        if (str_contains($stripped, 'CHASSIS')) {
            return 'CHASSISRENTAL';
        }

        if (str_contains($stripped, 'PROCESSING') && str_contains($stripped, 'NTC')) {
            return 'PROCESSINGNTC';
        }

        if (str_contains($stripped, 'PROCESSING') && (str_contains($stripped, 'IAS') || str_contains($stripped, 'AOCG'))) {
            return 'PROCESSINGIASAOCG';
        }

        if (str_contains($stripped, 'PROCESSING') && str_contains($stripped, 'ATRIG')) {
            return 'PROCESSINGATRIG';
        }

        if (str_contains($stripped, 'PROCESSING') && str_contains($stripped, 'WITHDRAWAL')) {
            return 'PROCESSINGWITHDRAWAL';
        }

        if (str_contains($stripped, 'PROCESSING')) {
            return 'PROCESSINGEXPENSES';
        }

        if (str_contains($stripped, 'TRUCKING')) {
            return 'TRUCKINGCHARGES';
        }

        if (str_contains($stripped, 'TABS')) {
            return 'TABS';
        }

        if (str_contains($stripped, 'EMPTYCONTAINER') || str_contains($stripped, 'EMPTYRETURN')) {
            return 'EMPTYRETURN';
        }

        if (str_contains($stripped, 'PENALTY')) {
            return 'LESSPENALTY';
        }

        return null;
    }

    private function shouldIgnoreCostDescription(?string $description): bool
    {
        $value = strtoupper(trim((string) $description));
        if ($value === '') {
            return true;
        }

        $stripped = preg_replace('/[^A-Z0-9]+/', '', $value);
        if ($stripped === null || $stripped === '') {
            return true;
        }

        $ignored = [
            'ARECEIPTEDREIMBURSEABLECHARGES',
            'ANONRECEIPTEDCHARGES',
            'RECEIPTEDREIMBURSEABLECHARGES',
            'NONRECEIPTEDCHARGES',
        ];

        return in_array($stripped, $ignored, true);
    }

    private function resolveVoucherDeductionType(ReimbursableVoucherItem $item): string
    {
        $type = strtolower(trim((string) ($item->deduction_type ?? 'none')));
        if (in_array($type, ['none', 'advance', 'penalty'], true) && $type !== 'none') {
            return $type;
        }

        $description = strtoupper(trim((string) ($item->description ?? '')));
        if ($description !== '') {
            if (str_contains($description, 'PENALTY')) {
                return 'penalty';
            }
            if (str_contains($description, 'ADVANCE')) {
                return 'advance';
            }
        }

        return 'none';
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
}
