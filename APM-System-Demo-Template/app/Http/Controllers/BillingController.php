<?php

namespace App\Http\Controllers;

use App\Models\BillingStatement;
use App\Models\CashAdvanceRequest;
use App\Models\Client;
use App\Models\DebitCreditNote;
use App\Models\JobOrder;
use App\Models\ServiceInvoice;
use App\Services\BillingPdfPackageService;
use App\Services\RecordMonitoringSyncService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct(
        private readonly RecordMonitoringSyncService $recordMonitoringSyncService,
        private readonly BillingPdfPackageService $billingPdfPackageService,
    )
    {
    }

    public function index(Request $request): View
    {
        return $this->renderIndex($request, false);
    }

    public function serviceIndex(Request $request): View
    {
        return $this->renderIndex($request, true);
    }

    public function masterStorage(Request $request): View
    {
        $search = $request->string('q')->toString();

        $billingStatements = BillingStatement::query()
            ->where('document_type', '!=', 'service_invoice')
            ->with(['jobOrder', 'createdBy'])
            ->get();

        $serviceInvoices = ServiceInvoice::query()
            ->with(['jobOrder', 'createdBy'])
            ->get();

        $notes = DebitCreditNote::query()
            ->with(['jobOrder', 'createdBy'])
            ->get();

        $groups = collect();

        $pushToGroup = function (int|string|null $jobOrderId, string $type, $doc) use (&$groups): void {
            $key = $jobOrderId ?: 'no-jo';
            if (!$groups->has($key)) {
                $groups->put($key, [
                    'job_order_id' => $jobOrderId,
                    'job_order' => $doc->jobOrder,
                    'billing_statements' => collect(),
                    'service_invoices' => collect(),
                    'debit_credit_notes' => collect(),
                ]);
            }

            $entry = $groups->get($key);
            if (!$entry['job_order'] && $doc->jobOrder) {
                $entry['job_order'] = $doc->jobOrder;
            }
            $entry[$type]->push($doc);
            $groups->put($key, $entry);
        };

        $billingStatements->each(fn ($doc) => $pushToGroup($doc->job_order_id, 'billing_statements', $doc));
        $serviceInvoices->each(fn ($doc) => $pushToGroup($doc->job_order_id, 'service_invoices', $doc));
        $notes->each(fn ($doc) => $pushToGroup($doc->job_order_id, 'debit_credit_notes', $doc));

        $groups = $groups
            ->map(function (array $group) {
                $group['billing_statements'] = $group['billing_statements']->sortByDesc('created_at')->values();
                $group['service_invoices'] = $group['service_invoices']->sortByDesc('created_at')->values();
                $group['debit_credit_notes'] = $group['debit_credit_notes']->sortByDesc('created_at')->values();
                $group['latest_at'] = collect([
                    optional($group['billing_statements']->first())->created_at,
                    optional($group['service_invoices']->first())->created_at,
                    optional($group['debit_credit_notes']->first())->created_at,
                ])->filter()->sortDesc()->first();

                return $group;
            })
            ->filter(function (array $group) use ($search) {
                if ($search === '') {
                    return true;
                }

                $jobOrder = $group['job_order'];
                $haystack = strtoupper(implode(' ', [
                    $jobOrder?->number ?? '',
                    $jobOrder?->code ?? '',
                    $jobOrder?->consignee ?? '',
                    $jobOrder?->shipper ?? '',
                ]));

                return str_contains($haystack, strtoupper($search));
            })
            ->sortByDesc('latest_at')
            ->values();

        return view('modules.billing.master-storage', [
            'groups' => $groups,
            'search' => $search,
        ]);
    }

    public function containerDeposits(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $perPage = 20;

        $entries = collect();

        BillingStatement::query()
            ->where('document_type', '!=', 'service_invoice')
            ->with(['jobOrder', 'createdBy'])
            ->get()
            ->each(function (BillingStatement $document) use (&$entries): void {
                $rows = $this->containerDepositRowsFromBillingData($document->data ?? []);
                if ($rows === []) {
                    return;
                }

                $entries->push($this->containerDepositEntry(
                    $document,
                    'Billing Statement',
                    (string) $document->statement_no,
                    route('billing.show', $document),
                    $rows
                ));
            });

        ServiceInvoice::query()
            ->with(['jobOrder', 'createdBy'])
            ->get()
            ->each(function (ServiceInvoice $document) use (&$entries): void {
                $rows = $this->containerDepositRowsFromBillingData($document->data ?? []);
                if ($rows === []) {
                    return;
                }

                $entries->push($this->containerDepositEntry(
                    $document,
                    'Service Invoice',
                    (string) $document->statement_no,
                    route('billing.service-invoices.show', $document),
                    $rows
                ));
            });

        DebitCreditNote::query()
            ->with(['jobOrder', 'createdBy'])
            ->get()
            ->each(function (DebitCreditNote $document) use (&$entries): void {
                $rows = $this->containerDepositRowsFromDebitCreditNoteData($document->data ?? []);
                if ($rows === []) {
                    return;
                }

                $entries->push($this->containerDepositEntry(
                    $document,
                    'Debit/Credit Note',
                    (string) $document->note_no,
                    route('billing.notes.show', $document),
                    $rows
                ));
            });

        if ($search !== '') {
            $needle = strtoupper($search);
            $entries = $entries->filter(function (array $entry) use ($needle): bool {
                $haystack = strtoupper(implode(' ', [
                    $entry['document_type'],
                    $entry['document_no'],
                    $entry['jo_no'],
                    $entry['jo_code'],
                    $entry['consignee'],
                    $entry['created_by'],
                    collect($entry['rows'])->pluck('description')->implode(' '),
                ]));

                return str_contains($haystack, $needle);
            });
        }

        $entries = $entries
            ->sortByDesc('sort_at')
            ->values();

        $page = max(1, $request->integer('page', 1));
        $containerDeposits = new \Illuminate\Pagination\LengthAwarePaginator(
            $entries->forPage($page, $perPage)->values(),
            $entries->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->except('page'),
            ]
        );

        return view('modules.billing.container-deposits', [
            'containerDeposits' => $containerDeposits,
            'search' => $search,
            'totalAmount' => $entries->sum('total_amount'),
            'totalRecords' => $entries->count(),
        ]);
    }

    private function containerDepositEntry(
        BillingStatement|ServiceInvoice|DebitCreditNote $document,
        string $documentType,
        string $documentNo,
        string $url,
        array $rows
    ): array {
        $jobOrder = $document->jobOrder;
        $date = $document instanceof DebitCreditNote
            ? ($document->note_date ?? $document->created_at)
            : $document->created_at;

        return [
            'document_type' => $documentType,
            'document_no' => $documentNo,
            'jo_no' => $jobOrder?->number ?? '-',
            'jo_code' => $jobOrder?->code ?? '-',
            'consignee' => $jobOrder?->consignee ?? '-',
            'created_by' => $document->createdBy?->name ?? '-',
            'date' => $date,
            'sort_at' => optional($date)->timestamp ?? 0,
            'rows' => $rows,
            'total_amount' => collect($rows)->sum('amount'),
            'url' => $url,
        ];
    }

    private function containerDepositRowsFromBillingData(array $data): array
    {
        $lineSets = [
            ['section' => 'Non-Receipted Charges', 'descriptions' => $data['non_receipted_desc'] ?? [], 'amounts' => $data['non_receipted_amount'] ?? []],
            ['section' => 'Receipted Charges', 'descriptions' => $data['receipted_desc'] ?? [], 'amounts' => $data['receipted_amount'] ?? []],
            ['section' => 'Service Charges', 'descriptions' => $data['si_item_description'] ?? [], 'amounts' => $data['si_amount'] ?? []],
        ];

        $rows = [];
        foreach ($lineSets as $lineSet) {
            $descriptions = is_array($lineSet['descriptions']) ? $lineSet['descriptions'] : [];
            $amounts = is_array($lineSet['amounts']) ? $lineSet['amounts'] : [];

            foreach ($descriptions as $index => $description) {
                $description = trim((string) $description);
                if (!$this->isContainerDepositDescription($description)) {
                    continue;
                }

                $rows[] = [
                    'section' => $lineSet['section'],
                    'description' => $description,
                    'amount' => $this->parseAmount($amounts[$index] ?? 0),
                ];
            }
        }

        return $rows;
    }

    private function containerDepositRowsFromDebitCreditNoteData(array $data): array
    {
        $rows = [];
        foreach (($data['rows'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }

            $description = trim((string) ($row['particular'] ?? ''));
            if (!$this->isContainerDepositDescription($description)) {
                continue;
            }

            $side = strtoupper((string) ($row['side'] ?? 'debit'));
            $rows[] = [
                'section' => $side === 'CREDIT' ? 'Credit' : 'Debit',
                'description' => $description,
                'amount' => $this->parseAmount($row['amount'] ?? 0),
            ];
        }

        return $rows;
    }

    private function isContainerDepositDescription(?string $description): bool
    {
        $normalized = preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $description)) ?? '';

        return str_contains($normalized, 'CONDEP')
            || str_contains($normalized, 'CONTAINERDEP')
            || str_contains($normalized, 'CONTAINERDEPOSIT');
    }

    private function renderIndex(Request $request, bool $isService): View
    {
        $search = $request->string('q')->toString();

        $jobOrders = JobOrder::query()
            ->withExists([
                'billingStatements as has_billing_statement' => function ($query) {
                    $query->where('document_type', '!=', 'service_invoice');
                },
                'serviceInvoices as has_service_invoice',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('consignee', 'like', "%{$search}%")
                    ->orWhere('shipper', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderByRaw('CAST(number AS UNSIGNED) DESC')
            ->orderByDesc('number')
            ->paginate(10)
            ->withQueryString();

        return view('modules.billing.index', [
            'jobOrders' => $jobOrders,
            'search' => $search,
            'documentType' => $isService ? 'service_invoice' : 'billing_statement',
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($request->string('type')->toString() === 'service_invoice') {
            return redirect()->route('billing.service-invoices.create', [
                'job_order_id' => $request->integer('job_order_id') ?: null,
            ]);
        }

        return $this->renderCreate($request, false);
    }

    public function createService(Request $request): View|RedirectResponse
    {
        return $this->renderCreate($request, true);
    }

    private function renderCreate(Request $request, bool $isService, BillingStatement|ServiceInvoice|null $statement = null): View|RedirectResponse
    {
        $jobOrderId = $statement?->job_order_id ?? $request->integer('job_order_id');
        $jobOrder = $jobOrderId ? JobOrder::query()->with('client')->find($jobOrderId) : null;

        $client = $this->resolveJobOrderClient($jobOrder);

        $jobRefNo = null;
        if ($jobOrder) {
            $year = $jobOrder->jo_date ? \Illuminate\Support\Carbon::parse($jobOrder->jo_date)->format('y') : now()->format('y');
            $jobRefNo = trim("{$jobOrder->code}-{$jobOrder->mo}-{$jobOrder->number}-{$year}", '-');
        }

        $existingBillingStatement = $jobOrder ? $this->findExistingBillingStatement($jobOrder->id) : null;
        $existingServiceInvoice = $jobOrder ? $this->findExistingServiceInvoice($jobOrder->id) : null;

        $view = $isService ? 'modules.billing.create-service' : 'modules.billing.create-billing';

        return view($view, [
            'jobOrder' => $jobOrder,
            'client' => $client,
            'jobRefNo' => $jobRefNo,
            'statementNo' => $isService ? $this->nextServiceInvoiceNumber() : $this->nextBillingStatementNumber(),
            'documentType' => $isService ? 'service_invoice' : 'billing_statement',
            'statement' => $statement,
            'existingBillingStatement' => $existingBillingStatement,
            'existingServiceInvoice' => $existingServiceInvoice,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'job_order_id' => ['nullable', 'exists:job_orders,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,bmp,tif,tiff', 'max:20480'],
        ]);

        $payload = $this->billingPayloadFromRequest($request);
        if (empty($payload['statement_date'])) {
            $payload['statement_date'] = now()->format('Y-m-d');
        }

        $statement = BillingStatement::create([
            'statement_no' => $this->nextBillingStatementNumber(),
            'document_type' => 'billing_statement',
            'job_order_id' => $validated['job_order_id'] ?? null,
            'created_by_user_id' => $request->user()?->id,
            'data' => $payload,
        ]);
        $this->storeBillingAttachments($request, $statement);

        $this->recordMonitoringSyncService->syncBillingStatement($statement);

        return redirect()
            ->route('billing')
            ->with('status', 'billing-created');
    }

    public function draft(Request $request): View
    {
        $validated = $request->validate([
            'job_order_id' => ['nullable', 'exists:job_orders,id'],
        ]);

        $payload = $this->billingPayloadFromRequest($request);
        $statement = BillingStatement::make([
            'statement_no' => $this->nextBillingStatementNumber(),
            'document_type' => 'billing_statement',
            'job_order_id' => $validated['job_order_id'] ?? null,
            'created_by_user_id' => $request->user()?->id,
            'data' => $payload,
        ]);
        $statement->created_at = now();
        $statement->setRelation('jobOrder', $statement->job_order_id ? JobOrder::query()->find($statement->job_order_id) : null);
        $statement->setRelation('createdBy', $request->user());

        $nonTotal = collect($payload['non_receipted_amount'] ?? [])
            ->sum(fn ($amount) => is_numeric($amount) ? (float) $amount : 0.0);
        $recTotal = collect($payload['receipted_amount'] ?? [])
            ->sum(fn ($amount) => is_numeric($amount) ? (float) $amount : 0.0);
        $baseGrandTotal = is_numeric($payload['grand_total'] ?? null)
            ? (float) $payload['grand_total']
            : ($nonTotal + $recTotal);
        $advanceTotal = $this->cashAdvanceAmountForJobOrder($statement->job_order_id);
        $deductAdvances = $this->deductAdvancesEnabled($payload);
        $remainingAdvance = $deductAdvances ? $this->remainingAdvanceAfterServiceInvoices($statement->job_order_id) : 0.0;
        $advanceDeduction = min($baseGrandTotal, $remainingAdvance);
        $advanceOverpayment = max(0, $remainingAdvance - $baseGrandTotal);
        $advanceBalanceAfterBilling = max(0, $remainingAdvance - $advanceDeduction);
        $adjustedGrandTotal = max(0, $baseGrandTotal - $advanceDeduction);
        $adjustedAmountInWords = $advanceOverpayment > 0
            ? $this->numberToWords($advanceOverpayment)
            : ($remainingAdvance > 0
                ? $this->numberToWords($adjustedGrandTotal)
                : ($payload['amount_in_words'] ?? ''));

        return view('modules.billing.show-billing', [
            'statement' => $statement,
            'advanceDeduction' => $advanceDeduction,
            'advanceOverpayment' => $advanceOverpayment,
            'advanceTotal' => $advanceTotal,
            'advanceAvailableForBilling' => $remainingAdvance,
            'advanceBalanceAfterBilling' => $advanceBalanceAfterBilling,
            'baseGrandTotal' => $baseGrandTotal,
            'adjustedGrandTotal' => $adjustedGrandTotal,
            'adjustedAmountInWords' => $adjustedAmountInWords,
            'isDraft' => true,
        ]);
    }

    public function storeService(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'job_order_id' => ['nullable', 'exists:job_orders,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,bmp,tif,tiff', 'max:20480'],
        ]);

        $payload = $this->billingPayloadFromRequest($request);
        if (empty($payload['statement_date'])) {
            $payload['statement_date'] = now()->format('Y-m-d');
        }

        $serviceInvoice = ServiceInvoice::create([
            'statement_no' => $this->nextServiceInvoiceNumber(),
            'job_order_id' => $validated['job_order_id'] ?? null,
            'created_by_user_id' => $request->user()?->id,
            'data' => $payload,
        ]);
        $this->storeBillingAttachments($request, $serviceInvoice);

        $this->recordMonitoringSyncService->syncServiceInvoice($serviceInvoice);

        return redirect()
            ->route('billing.service-invoices')
            ->with('status', 'billing-created');
    }

    public function draftService(Request $request): View
    {
        $validated = $request->validate([
            'job_order_id' => ['nullable', 'exists:job_orders,id'],
        ]);

        $payload = $this->billingPayloadFromRequest($request);
        $serviceInvoice = ServiceInvoice::make([
            'statement_no' => $this->nextServiceInvoiceNumber(),
            'job_order_id' => $validated['job_order_id'] ?? null,
            'created_by_user_id' => $request->user()?->id,
            'data' => $payload,
        ]);
        $serviceInvoice->created_at = now();
        $serviceInvoice->setRelation('jobOrder', $serviceInvoice->job_order_id ? JobOrder::query()->find($serviceInvoice->job_order_id) : null);
        $serviceInvoice->setRelation('createdBy', $request->user());
        $baseServiceTotal = $this->serviceInvoiceTotalFromData($payload);
        $totalAdvance = $this->cashAdvanceAmountForJobOrder($serviceInvoice->job_order_id);
        $deductAdvances = $this->deductAdvancesEnabled($payload);
        $serviceAdvanceDeduction = $deductAdvances ? min($baseServiceTotal, $totalAdvance) : 0.0;
        $adjustedServiceTotal = max(0, $baseServiceTotal - $serviceAdvanceDeduction);

        return view('modules.billing.show-service', [
            'statement' => $serviceInvoice,
            'serviceAdvanceDeduction' => $serviceAdvanceDeduction,
            'serviceAdvanceBalance' => max(0, $totalAdvance - $serviceAdvanceDeduction),
            'baseServiceTotal' => $baseServiceTotal,
            'adjustedServiceTotal' => $adjustedServiceTotal,
            'adjustedAmountInWords' => $serviceAdvanceDeduction > 0 ? $this->numberToWords($adjustedServiceTotal) : ($payload['amount_in_words'] ?? ''),
            'isDraft' => true,
        ]);
    }

    public function documents(Request $request): View
    {
        return $this->renderDocuments($request, false);
    }

    public function serviceDocuments(Request $request): View
    {
        return $this->renderDocuments($request, true);
    }

    public function notes(Request $request): View
    {
        $search = $request->string('q')->toString();

        $jobOrders = JobOrder::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('consignee', 'like', "%{$search}%")
                    ->orWhere('shipper', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderByRaw('CAST(number AS UNSIGNED) DESC')
            ->orderByDesc('number')
            ->limit(50)
            ->get();

        return view('modules.billing.notes', [
            'jobOrders' => $jobOrders,
            'search' => $search,
        ]);
    }

    public function createNote(Request $request): View|RedirectResponse
    {
        $jobOrder = null;
        $client = null;
        $jobRefNo = '';

        $jobOrderId = $request->integer('job_order_id');
        if (!$jobOrderId) {
            return redirect()
                ->route('billing.notes')
                ->with('status', 'dcn-select-jo-first');
        }

        if ($jobOrderId) {
            $jobOrder = JobOrder::query()->with('client')->find($jobOrderId);
            if (!$jobOrder) {
                return redirect()
                    ->route('billing.notes')
                    ->with('status', 'dcn-select-jo-first');
            }
            if ($jobOrder) {
                $client = $this->resolveJobOrderClient($jobOrder);

                $year = $jobOrder->jo_date
                    ? \Illuminate\Support\Carbon::parse($jobOrder->jo_date)->format('y')
                    : now()->format('y');
                $jobRefNo = trim("{$jobOrder->code}-{$jobOrder->mo}-{$jobOrder->number}-{$year}", '-');
            }
        }

        $jobOrders = collect([$jobOrder]);

        $clientsByName = Client::query()
            ->get()
            ->keyBy('name');

        return view('modules.billing.create-note', [
            'jobOrder' => $jobOrder,
            'client' => $client,
            'jobRefNo' => $jobRefNo,
            'jobOrders' => $jobOrders,
            'clientsByName' => $clientsByName,
            'nextNoteNo' => $this->nextDebitCreditNoteNumber(),
        ]);
    }

    private function resolveJobOrderClient(?JobOrder $jobOrder): ?Client
    {
        if (!$jobOrder) {
            return null;
        }

        if ($jobOrder->relationLoaded('client') && $jobOrder->client) {
            return $jobOrder->client;
        }

        if ($jobOrder->client_id) {
            $client = Client::query()->find($jobOrder->client_id);
            if ($client) {
                return $client;
            }
        }

        if (!$jobOrder->consignee) {
            return null;
        }

        return Client::query()
            ->where('name', $jobOrder->consignee)
            ->orderBy('id')
            ->first();
    }

    public function storeNote(Request $request): RedirectResponse
    {
        $notePayload = $this->debitCreditNotePayloadFromRequest($request);

        $note = DebitCreditNote::create($notePayload + [
            'note_no' => $this->nextDebitCreditNoteNumber(),
            'created_by_user_id' => $request->user()?->id,
        ]);

        $this->recordMonitoringSyncService->syncDebitCreditNote($note);

        return redirect()
            ->route('billing.notes.documents')
            ->with('status', 'debit-credit-note-saved');
    }

    public function editNote(DebitCreditNote $debitCreditNote): View|RedirectResponse
    {
        $debitCreditNote->load(['jobOrder.client', 'attachments']);
        $jobOrder = $debitCreditNote->jobOrder;

        if (!$jobOrder) {
            return redirect()
                ->route('billing.notes.documents')
                ->withErrors(['job_order_id' => 'This Debit/Credit Note has no linked Job Order to edit.']);
        }

        $client = $this->resolveJobOrderClient($jobOrder);
        $year = $jobOrder->jo_date
            ? \Illuminate\Support\Carbon::parse($jobOrder->jo_date)->format('y')
            : now()->format('y');
        $jobRefNo = trim("{$jobOrder->code}-{$jobOrder->mo}-{$jobOrder->number}-{$year}", '-');

        return view('modules.billing.create-note', [
            'note' => $debitCreditNote,
            'jobOrder' => $jobOrder,
            'client' => $client,
            'jobRefNo' => $jobRefNo,
            'jobOrders' => collect([$jobOrder]),
            'clientsByName' => Client::query()->get()->keyBy('name'),
            'nextNoteNo' => $debitCreditNote->note_no,
        ]);
    }

    public function updateNote(Request $request, DebitCreditNote $debitCreditNote): RedirectResponse
    {
        $notePayload = $this->debitCreditNotePayloadFromRequest($request);

        $debitCreditNote->update($notePayload);
        $this->recordMonitoringSyncService->syncDebitCreditNote($debitCreditNote->fresh(['jobOrder.assignedUser', 'createdBy']));

        return redirect()
            ->route('billing.notes.show', ['debitCreditNote' => $debitCreditNote->id, 'v' => time()])
            ->with('status', 'debit-credit-note-updated');
    }

    private function debitCreditNotePayloadFromRequest(Request $request): array
    {
        $validated = $request->validate([
            'job_order_id' => ['required', 'exists:job_orders,id'],
            'note_date' => ['required', 'date'],
            'bill_to' => ['required', 'string', 'max:255'],
            'bill_address' => ['nullable', 'string', 'max:255'],
            'bill_tin' => ['nullable', 'string', 'max:100'],
            'bill_business_style' => ['nullable', 'string', 'max:255'],
            'vessel_voyage' => ['nullable', 'string', 'max:255'],
            'bl_no' => ['nullable', 'string', 'max:255'],
            'vol_meas' => ['nullable', 'string', 'max:100'],
            'vol_meas_unit' => ['nullable', 'string', 'max:20'],
            'job_ref_no' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'shipper_name' => ['nullable', 'string', 'max:255'],
            'invoice_no' => ['nullable', 'string', 'max:255'],
            'port' => ['nullable', 'string', 'max:255'],
            'container_no' => ['nullable', 'string', 'max:255'],
            'amount_in_words' => ['nullable', 'string', 'max:500'],
            'dcn_particular' => ['required', 'array', 'min:1'],
            'dcn_particular.*' => ['nullable', 'string', 'max:255'],
            'dcn_side' => ['required', 'array'],
            'dcn_side.*' => ['nullable', 'in:debit,credit'],
            'dcn_amount' => ['required', 'array'],
            'dcn_amount.*' => ['nullable', 'numeric', 'min:0'],
            'deduct_advances' => ['nullable', 'boolean'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $rows = [];
        $totalDebit = 0.0;
        $totalCredit = 0.0;
        $particulars = $validated['dcn_particular'] ?? [];
        $sides = $validated['dcn_side'] ?? [];
        $amounts = $validated['dcn_amount'] ?? [];

        $maxRows = max(count($particulars), count($sides), count($amounts));
        for ($i = 0; $i < $maxRows; $i++) {
            $particular = trim((string) ($particulars[$i] ?? ''));
            $side = strtolower((string) ($sides[$i] ?? 'debit'));
            $amount = is_numeric($amounts[$i] ?? null) ? (float) $amounts[$i] : 0.0;

            if ($particular === '' && $amount <= 0) {
                continue;
            }

            if ($side !== 'credit') {
                $side = 'debit';
            }

            if ($side === 'debit') {
                $totalDebit += $amount;
            } else {
                $totalCredit += $amount;
            }

            $rows[] = [
                'particular' => $particular,
                'side' => $side,
                'amount' => $amount,
            ];
        }

        if (count($rows) === 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'dcn_particular' => 'Add at least one debit/credit line.',
            ]);
        }

        $netTotal = $totalDebit - $totalCredit;

        $notePayload = [
            'job_order_id' => $validated['job_order_id'],
            'note_type' => 'debit',
            'note_date' => $validated['note_date'],
            'amount' => $netTotal,
            'description' => $validated['description'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'data' => [
                'bill_to' => $validated['bill_to'],
                'bill_address' => $validated['bill_address'] ?? '',
                'bill_tin' => $validated['bill_tin'] ?? '',
                'bill_business_style' => $validated['bill_business_style'] ?? '',
                'vessel_voyage' => $validated['vessel_voyage'] ?? '',
                'bl_no' => $validated['bl_no'] ?? '',
                'vol_meas' => $validated['vol_meas'] ?? '',
                'vol_meas_unit' => $validated['vol_meas_unit'] ?? '',
                'job_ref_no' => $validated['job_ref_no'] ?? '',
                'description' => $validated['description'] ?? '',
                'shipper_name' => $validated['shipper_name'] ?? '',
                'invoice_no' => $validated['invoice_no'] ?? '',
                'port' => $validated['port'] ?? '',
                'container_no' => $validated['container_no'] ?? '',
                'amount_in_words' => $validated['amount_in_words'] ?? '',
                'rows' => $rows,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'net_total' => $netTotal,
                'deduct_advances' => $request->boolean('deduct_advances'),
                'include_in_cost_sheet' => true,
                'remarks' => $validated['remarks'] ?? '',
            ],
        ];

        return $notePayload;
    }

    public function showNote(DebitCreditNote $debitCreditNote): View
    {
        $debitCreditNote->load(['jobOrder', 'createdBy', 'attachments']);
        $baseNoteTotal = $this->debitCreditNoteChargeTotalFromData($debitCreditNote->data ?? []);
        $deductAdvances = $this->deductAdvancesEnabled($debitCreditNote->data ?? []);
        $noteAdvanceDeduction = $deductAdvances ? $this->debitCreditNoteAdvanceDeductionForNote($debitCreditNote) : 0.0;
        $adjustedNoteTotal = max(0, $baseNoteTotal - $noteAdvanceDeduction);

        return view('modules.billing.show-note', [
            'note' => $debitCreditNote,
            'baseNoteTotal' => $baseNoteTotal,
            'noteAdvanceDeduction' => $noteAdvanceDeduction,
            'noteAdvanceTotal' => $this->cashAdvanceAmountForJobOrder($debitCreditNote->job_order_id),
            'noteAdvanceBalance' => $deductAdvances ? $this->debitCreditNoteAdvanceBalanceAfterNote($debitCreditNote) : 0.0,
            'adjustedNoteTotal' => $adjustedNoteTotal,
            'adjustedAmountInWords' => $noteAdvanceDeduction > 0 ? $this->numberToWords($adjustedNoteTotal) : ($debitCreditNote->data['amount_in_words'] ?? ''),
        ]);
    }

    public function noteDocuments(Request $request): View
    {
        $search = $request->string('q')->toString();
        $jobOrderId = $request->integer('job_order_id') ?: null;

        $notes = DebitCreditNote::query()
            ->with(['jobOrder', 'createdBy'])
            ->when($jobOrderId, function ($query) use ($jobOrderId) {
                $query->where('job_order_id', $jobOrderId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('jobOrder', function ($jobOrderQuery) use ($search) {
                    $jobOrderQuery->where('number', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('consignee', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('note_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('modules.billing.note-documents', [
            'notes' => $notes,
            'search' => $search,
            'jobOrderId' => $jobOrderId,
        ]);
    }

    private function renderDocuments(Request $request, bool $isService): View
    {
        $jobOrderId = $request->integer('job_order_id') ?: null;

        $statements = $isService
            ? ServiceInvoice::query()
                ->when($jobOrderId, function ($query) use ($jobOrderId) {
                    $query->where('job_order_id', $jobOrderId);
                })
                ->with(['jobOrder', 'createdBy'])
                ->orderByDesc('created_at')
                ->paginate(15)
            : BillingStatement::query()
                ->when($jobOrderId, function ($query) use ($jobOrderId) {
                    $query->where('job_order_id', $jobOrderId);
                })
                ->where('document_type', '!=', 'service_invoice')
                ->with(['jobOrder', 'createdBy'])
                ->orderByDesc('created_at')
                ->paginate(15);

        return view('modules.billing.documents', [
            'statements' => $statements,
            'documentType' => $isService ? 'service_invoice' : 'billing_statement',
            'jobOrderId' => $jobOrderId,
        ]);
    }

    public function show(BillingStatement $billingStatement): View
    {
        $billingStatement->load(['jobOrder', 'createdBy', 'attachments']);

        $data = $billingStatement->data ?? [];
        $nonTotal = collect($data['non_receipted_amount'] ?? [])
            ->sum(fn ($amount) => is_numeric($amount) ? (float) $amount : 0.0);
        $recTotal = collect($data['receipted_amount'] ?? [])
            ->sum(fn ($amount) => is_numeric($amount) ? (float) $amount : 0.0);
        $baseGrandTotal = is_numeric($data['grand_total'] ?? null)
            ? (float) $data['grand_total']
            : ($nonTotal + $recTotal);
        $advanceTotal = $this->cashAdvanceAmountForJobOrder($billingStatement->job_order_id);
        $deductAdvances = $this->deductAdvancesEnabled($data);
        $remainingAdvance = $deductAdvances ? $this->remainingAdvanceAfterServiceInvoices($billingStatement->job_order_id) : 0.0;
        $advanceDeduction = min($baseGrandTotal, $remainingAdvance);
        $advanceOverpayment = max(0, $remainingAdvance - $baseGrandTotal);
        $advanceBalanceAfterBilling = max(0, $remainingAdvance - $advanceDeduction);
        $adjustedGrandTotal = max(0, $baseGrandTotal - $advanceDeduction);
        $adjustedAmountInWords = $advanceOverpayment > 0
            ? $this->numberToWords($advanceOverpayment)
            : ($remainingAdvance > 0
                ? $this->numberToWords($adjustedGrandTotal)
                : ($data['amount_in_words'] ?? ''));

        return view('modules.billing.show-billing', [
            'statement' => $billingStatement,
            'advanceDeduction' => $advanceDeduction,
            'advanceOverpayment' => $advanceOverpayment,
            'advanceTotal' => $advanceTotal,
            'advanceAvailableForBilling' => $remainingAdvance,
            'advanceBalanceAfterBilling' => $advanceBalanceAfterBilling,
            'baseGrandTotal' => $baseGrandTotal,
            'adjustedGrandTotal' => $adjustedGrandTotal,
            'adjustedAmountInWords' => $adjustedAmountInWords,
        ]);
    }

    private function billingPayloadFromRequest(Request $request): array
    {
        $payload = $request->except(['_token', '_method', 'document_type', 'attachments']);
        if (empty($payload['statement_date'])) {
            $payload['statement_date'] = now()->format('Y-m-d');
        }

        if (isset($payload['si_less_vat'])) {
            $payload['si_add_vat'] = $payload['si_less_vat'];
        }

        return $payload;
    }

    public function downloadPdf(BillingStatement $billingStatement)
    {
        $pdf = $this->billingPdfPackageService->make($billingStatement);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $this->billingPdfPackageService->filename($billingStatement) . '"',
        ]);
    }

    public function downloadServicePdf(ServiceInvoice $serviceInvoice)
    {
        $pdf = $this->billingPdfPackageService->make($serviceInvoice);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $this->billingPdfPackageService->filename($serviceInvoice) . '"',
        ]);
    }

    public function storeBillingStatementAttachments(Request $request, BillingStatement $billingStatement): RedirectResponse
    {
        $this->validateAttachmentUpload($request);
        $this->storeBillingAttachments($request, $billingStatement);

        return redirect()
            ->route('billing.show', $billingStatement)
            ->with('status', 'billing-attachments-uploaded');
    }

    public function storeServiceInvoiceAttachments(Request $request, ServiceInvoice $serviceInvoice): RedirectResponse
    {
        $this->validateAttachmentUpload($request);
        $this->storeBillingAttachments($request, $serviceInvoice);

        return redirect()
            ->route('service-invoices.show', $serviceInvoice)
            ->with('status', 'billing-attachments-uploaded');
    }

    public function storeDebitCreditNoteAttachments(Request $request, DebitCreditNote $debitCreditNote): RedirectResponse
    {
        $this->validateAttachmentUpload($request);
        $this->storeBillingAttachments($request, $debitCreditNote);

        return redirect()
            ->route('billing.notes.show', $debitCreditNote)
            ->with('status', 'billing-attachments-uploaded');
    }

    private function validateAttachmentUpload(Request $request): void
    {
        $request->validate([
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,bmp,tif,tiff', 'max:20480'],
        ]);
    }

    private function storeBillingAttachments(Request $request, BillingStatement|ServiceInvoice|DebitCreditNote $document): void
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('billing-attachments', 'public');
            $document->attachments()->create([
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize() ?: 0,
                'uploaded_by_user_id' => $request->user()?->id,
            ]);
        }
    }

    public function showService(ServiceInvoice $serviceInvoice): View
    {
        $serviceInvoice->load(['jobOrder', 'createdBy', 'attachments']);
        $baseServiceTotal = $this->serviceInvoiceTotalFromData($serviceInvoice->data ?? []);
        $deductAdvances = $this->deductAdvancesEnabled($serviceInvoice->data ?? []);
        $serviceAdvanceDeduction = $deductAdvances ? $this->serviceAdvanceDeductionForInvoice($serviceInvoice) : 0.0;
        $adjustedServiceTotal = max(0, $baseServiceTotal - $serviceAdvanceDeduction);

        return view('modules.billing.show-service', [
            'statement' => $serviceInvoice,
            'serviceAdvanceDeduction' => $serviceAdvanceDeduction,
            'serviceAdvanceBalance' => $deductAdvances ? $this->serviceAdvanceBalanceAfterInvoice($serviceInvoice) : 0.0,
            'baseServiceTotal' => $baseServiceTotal,
            'adjustedServiceTotal' => $adjustedServiceTotal,
            'adjustedAmountInWords' => $serviceAdvanceDeduction > 0 ? $this->numberToWords($adjustedServiceTotal) : ($serviceInvoice->data['amount_in_words'] ?? ''),
        ]);
    }

    public function edit(BillingStatement $billingStatement): View|RedirectResponse
    {
        $billingStatement->load(['jobOrder', 'attachments']);
        return $this->renderCreate(request(), false, $billingStatement);
    }

    public function editService(ServiceInvoice $serviceInvoice): View|RedirectResponse
    {
        $serviceInvoice->load(['jobOrder', 'attachments']);
        return $this->renderCreate(request(), true, $serviceInvoice);
    }

    public function update(Request $request, BillingStatement $billingStatement): RedirectResponse
    {
        $validated = $request->validate([
            'job_order_id' => ['nullable', 'exists:job_orders,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,bmp,tif,tiff', 'max:20480'],
        ]);

        $incoming = $this->billingPayloadFromRequest($request);
        $payload = array_merge($billingStatement->data ?? [], $incoming);
        if (empty($payload['statement_date'])) {
            $payload['statement_date'] = now()->format('Y-m-d');
        }

        $billingStatement->update([
            'document_type' => 'billing_statement',
            'job_order_id' => $validated['job_order_id'] ?? $billingStatement->job_order_id,
            'data' => $payload,
        ]);
        $this->storeBillingAttachments($request, $billingStatement);

        $this->recordMonitoringSyncService->syncBillingStatement($billingStatement->fresh(['jobOrder.assignedUser', 'createdBy']));

        return redirect()
            ->route('billing.show', ['billingStatement' => $billingStatement->id, 'v' => time()])
            ->with('status', 'billing-updated');
    }

    public function updateService(Request $request, ServiceInvoice $serviceInvoice): RedirectResponse
    {
        $validated = $request->validate([
            'job_order_id' => ['nullable', 'exists:job_orders,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,bmp,tif,tiff', 'max:20480'],
        ]);

        $incoming = $this->billingPayloadFromRequest($request);
        $payload = array_merge($serviceInvoice->data ?? [], $incoming);
        if (empty($payload['statement_date'])) {
            $payload['statement_date'] = now()->format('Y-m-d');
        }

        $serviceInvoice->update([
            'job_order_id' => $validated['job_order_id'] ?? $serviceInvoice->job_order_id,
            'data' => $payload,
        ]);
        $this->storeBillingAttachments($request, $serviceInvoice);

        $this->recordMonitoringSyncService->syncServiceInvoice($serviceInvoice->fresh(['jobOrder.assignedUser', 'createdBy']));

        return redirect()
            ->route('billing.service-invoices.show', ['serviceInvoice' => $serviceInvoice->id, 'v' => time()])
            ->with('status', 'billing-updated');
    }

    private function nextBillingStatementNumber(): int
    {
        $max = BillingStatement::query()
            ->where('document_type', '!=', 'service_invoice')
            ->max('statement_no');
        return $max ? max(((int) $max + 1), 8000) : 8000;
    }

    private function nextServiceInvoiceNumber(): int
    {
        $max = ServiceInvoice::query()->max('statement_no');
        return $max ? max(((int) $max + 1), 8000) : 8000;
    }

    private function nextDebitCreditNoteNumber(): string
    {
        $last = DebitCreditNote::query()
            ->orderByDesc('id')
            ->value('note_no');

        $next = 8000;
        if (is_string($last) && preg_match('/^DCN-(\d+)$/', $last, $matches)) {
            $next = max(((int) $matches[1]) + 1, 8000);
        }

        return 'DCN-' . (string) $next;
    }

    private function findExistingBillingStatement(int $jobOrderId): ?BillingStatement
    {
        return BillingStatement::query()
            ->where('job_order_id', $jobOrderId)
            ->where('document_type', '!=', 'service_invoice')
            ->orderByDesc('created_at')
            ->first();
    }

    private function findExistingServiceInvoice(int $jobOrderId): ?ServiceInvoice
    {
        return ServiceInvoice::query()
            ->where('job_order_id', $jobOrderId)
            ->orderByDesc('created_at')
            ->first();
    }

    private function cashAdvanceAmountForJobOrder(?int $jobOrderId): float
    {
        if (!$jobOrderId) {
            return 0.0;
        }

        $jobOrder = JobOrder::query()->find($jobOrderId);
        $joNumber = trim((string) ($jobOrder?->number ?? ''));
        if ($joNumber === '') {
            return 0.0;
        }

        $cashAdvanceBalance = CashAdvanceRequest::query()
            ->where('status', 'Approved')
            ->where('is_personal', false)
            ->whereHas('items', function ($query) use ($joNumber) {
                $query->where('jo_number', 'like', "%{$joNumber}%");
            })
            ->with(['items', 'liquidations' => function ($query) {
                $query->where('status', 'Approved');
            }])
            ->get()
            ->sum(function (CashAdvanceRequest $cashAdvance) use ($joNumber): float {
                $advanceAmount = $cashAdvance->items
                    ->filter(fn ($item) => $this->extractJoNumber((string) $item->jo_number) === $joNumber)
                    ->sum(fn ($item) => $this->parseAmount($item->amount ?? null));

                $liquidatedAmount = $cashAdvance->liquidations
                    ->filter(fn ($liquidation) => $this->extractJoNumber((string) $liquidation->jo_number) === $joNumber)
                    ->sum(fn ($liquidation) => $this->parseAmount($liquidation->amount ?? null));

                return max(0, $advanceAmount - $liquidatedAmount);
            });

        return round($cashAdvanceBalance + $this->debitCreditNoteAdvanceAmountForJobOrder($jobOrderId), 2);
    }

    private function debitCreditNoteAdvanceAmountForJobOrder(int $jobOrderId): float
    {
        return DebitCreditNote::query()
            ->where('job_order_id', $jobOrderId)
            ->get()
            ->sum(function (DebitCreditNote $note): float {
                $rows = collect($note->data['rows'] ?? []);

                return $rows->sum(function ($row): float {
                    $particular = strtoupper(trim((string) ($row['particular'] ?? '')));
                    $amount = $this->parseAmount($row['amount'] ?? null);

                    if ($particular === '' || !str_contains($particular, 'ADVANCE')) {
                        return 0.0;
                    }

                    return $amount;
                });
            });
    }

    private function remainingAdvanceAfterServiceInvoices(?int $jobOrderId): float
    {
        $advanceAmount = $this->cashAdvanceAmountForJobOrder($jobOrderId);
        if ($advanceAmount <= 0 || !$jobOrderId) {
            return 0.0;
        }

        $serviceTotal = $this->serviceAdvanceConsumedForJobOrder($jobOrderId, $advanceAmount);
        $advanceAfterService = max(0, $advanceAmount - $serviceTotal);
        $noteTotal = DebitCreditNote::query()
            ->where('job_order_id', $jobOrderId)
            ->get()
            ->filter(fn (DebitCreditNote $note): bool => $this->deductAdvancesEnabled($note->data ?? []))
            ->sum(fn (DebitCreditNote $note): float => $this->debitCreditNoteChargeTotalFromData($note->data ?? []));

        return max(0, round($advanceAfterService - min($advanceAfterService, (float) $noteTotal), 2));
    }

    private function serviceAdvanceDeductionForInvoice(ServiceInvoice $serviceInvoice): float
    {
        $advanceRemaining = $this->cashAdvanceAmountForJobOrder($serviceInvoice->job_order_id);
        if ($advanceRemaining <= 0) {
            return 0.0;
        }

        $serviceInvoices = ServiceInvoice::query()
            ->where('job_order_id', $serviceInvoice->job_order_id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        foreach ($serviceInvoices as $invoice) {
            if (!$this->deductAdvancesEnabled($invoice->data ?? [])) {
                continue;
            }

            $invoiceTotal = $this->serviceInvoiceTotalFromData($invoice->data ?? []);
            $deduction = min($invoiceTotal, $advanceRemaining);

            if ((int) $invoice->id === (int) $serviceInvoice->id) {
                return round($deduction, 2);
            }

            $advanceRemaining = max(0, $advanceRemaining - $deduction);
            if ($advanceRemaining <= 0) {
                return 0.0;
            }
        }

        return 0.0;
    }

    private function serviceAdvanceBalanceAfterInvoice(ServiceInvoice $serviceInvoice): float
    {
        $advanceRemaining = $this->cashAdvanceAmountForJobOrder($serviceInvoice->job_order_id);
        if ($advanceRemaining <= 0) {
            return 0.0;
        }

        $serviceInvoices = ServiceInvoice::query()
            ->where('job_order_id', $serviceInvoice->job_order_id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        foreach ($serviceInvoices as $invoice) {
            if (!$this->deductAdvancesEnabled($invoice->data ?? [])) {
                continue;
            }

            $invoiceTotal = $this->serviceInvoiceTotalFromData($invoice->data ?? []);
            $deduction = min($invoiceTotal, $advanceRemaining);
            $advanceRemaining = max(0, $advanceRemaining - $deduction);

            if ((int) $invoice->id === (int) $serviceInvoice->id) {
                return round($advanceRemaining, 2);
            }
        }

        return round($advanceRemaining, 2);
    }

    private function serviceInvoiceTotalFromData(array $data): float
    {
        $totalAmountDue = $this->parseAmount($data['si_total_amount_due'] ?? null);
        if ($totalAmountDue > 0) {
            return $totalAmountDue;
        }

        $grandTotal = $this->parseAmount($data['grand_total'] ?? null);
        if ($grandTotal > 0) {
            return $grandTotal;
        }

        $amounts = $data['si_amount'] ?? [];
        if (!is_array($amounts)) {
            $amounts = [$amounts];
        }

        return collect($amounts)->sum(fn ($amount) => $this->parseAmount($amount));
    }

    private function serviceAdvanceConsumedForJobOrder(?int $jobOrderId, ?float $advanceAmount = null): float
    {
        if (!$jobOrderId) {
            return 0.0;
        }

        $advanceAmount ??= $this->cashAdvanceAmountForJobOrder($jobOrderId);
        if ($advanceAmount <= 0) {
            return 0.0;
        }

        $serviceTotal = ServiceInvoice::query()
            ->where('job_order_id', $jobOrderId)
            ->get()
            ->filter(fn (ServiceInvoice $invoice): bool => $this->deductAdvancesEnabled($invoice->data ?? []))
            ->sum(fn (ServiceInvoice $invoice): float => $this->serviceInvoiceTotalFromData($invoice->data ?? []));

        return min($advanceAmount, round((float) $serviceTotal, 2));
    }

    private function debitCreditNoteAdvanceDeductionForNote(DebitCreditNote $note): float
    {
        if ($this->debitCreditNoteAdvanceSourceAmountFromData($note->data ?? []) > 0) {
            return 0.0;
        }

        $advanceRemaining = $this->cashAdvanceAmountForJobOrder($note->job_order_id);
        if ($advanceRemaining <= 0) {
            return 0.0;
        }

        $advanceRemaining = max(0, $advanceRemaining - $this->serviceAdvanceConsumedForJobOrder($note->job_order_id, $advanceRemaining));

        $notes = DebitCreditNote::query()
            ->where('job_order_id', $note->job_order_id)
            ->orderBy('note_date')
            ->orderBy('id')
            ->get();

        foreach ($notes as $candidate) {
            if ($this->debitCreditNoteAdvanceSourceAmountFromData($candidate->data ?? []) > 0) {
                continue;
            }
            if (!$this->deductAdvancesEnabled($candidate->data ?? [])) {
                continue;
            }

            $candidateTotal = $this->debitCreditNoteChargeTotalFromData($candidate->data ?? []);
            $deduction = min($candidateTotal, $advanceRemaining);

            if ((int) $candidate->id === (int) $note->id) {
                return round($deduction, 2);
            }

            $advanceRemaining = max(0, $advanceRemaining - $deduction);
            if ($advanceRemaining <= 0) {
                return 0.0;
            }
        }

        return 0.0;
    }

    private function debitCreditNoteAdvanceBalanceAfterNote(DebitCreditNote $note): float
    {
        if ($this->debitCreditNoteAdvanceSourceAmountFromData($note->data ?? []) > 0) {
            return 0.0;
        }

        $advanceRemaining = $this->cashAdvanceAmountForJobOrder($note->job_order_id);
        if ($advanceRemaining <= 0) {
            return 0.0;
        }

        $advanceRemaining = max(0, $advanceRemaining - $this->serviceAdvanceConsumedForJobOrder($note->job_order_id, $advanceRemaining));

        $notes = DebitCreditNote::query()
            ->where('job_order_id', $note->job_order_id)
            ->orderBy('note_date')
            ->orderBy('id')
            ->get();

        foreach ($notes as $candidate) {
            if ($this->debitCreditNoteAdvanceSourceAmountFromData($candidate->data ?? []) > 0) {
                continue;
            }
            if (!$this->deductAdvancesEnabled($candidate->data ?? [])) {
                continue;
            }

            $candidateTotal = $this->debitCreditNoteChargeTotalFromData($candidate->data ?? []);
            $deduction = min($candidateTotal, $advanceRemaining);
            $advanceRemaining = max(0, $advanceRemaining - $deduction);

            if ((int) $candidate->id === (int) $note->id) {
                return round($advanceRemaining, 2);
            }
        }

        return round($advanceRemaining, 2);
    }

    private function debitCreditNoteChargeTotalFromData(array $data): float
    {
        $rows = $data['rows'] ?? [];
        if (!is_array($rows)) {
            return 0.0;
        }

        $total = collect($rows)->sum(function ($row): float {
            $particular = (string) ($row['particular'] ?? '');
            if ($this->isAdvanceParticular($particular)) {
                return 0.0;
            }

            $side = strtolower((string) ($row['side'] ?? 'debit'));
            $amount = $this->parseAmount($row['amount'] ?? null);

            return $side === 'credit' ? -$amount : $amount;
        });

        return max(0, round((float) $total, 2));
    }

    private function debitCreditNoteAdvanceSourceAmountFromData(array $data): float
    {
        $rows = $data['rows'] ?? [];
        if (!is_array($rows)) {
            return 0.0;
        }

        return collect($rows)->sum(function ($row): float {
            $particular = (string) ($row['particular'] ?? '');
            $amount = $this->parseAmount($row['amount'] ?? null);

            return $this->isAdvanceParticular($particular) ? abs($amount) : 0.0;
        });
    }

    private function isAdvanceParticular(?string $particular): bool
    {
        $value = strtoupper(trim((string) $particular));

        return $value !== '' && str_contains($value, 'ADVANCE');
    }

    private function parseAmount(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $normalized = trim((string) $value);
        if ($normalized === '') {
            return 0.0;
        }

        $isNegative = str_starts_with($normalized, '(') && str_ends_with($normalized, ')');
        $normalized = preg_replace('/[^0-9.\-]/', '', $normalized) ?? '';
        if ($normalized === '' || $normalized === '-' || $normalized === '.') {
            return 0.0;
        }

        $amount = is_numeric($normalized) ? (float) $normalized : 0.0;

        return $isNegative ? -abs($amount) : $amount;
    }

    private function deductAdvancesEnabled(array $data): bool
    {
        if (!array_key_exists('deduct_advances', $data)) {
            return true;
        }

        return filter_var($data['deduct_advances'], FILTER_VALIDATE_BOOLEAN);
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

        if (preg_match('/(\d{3,})/', $value, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function numberToWords(float $num): string
    {
        $ones = [
            0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
        ];
        $tens = [
            2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
            6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety',
        ];
        $scales = ['', 'Thousand', 'Million', 'Billion'];

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
        $centsText = $cents > 0 ? ' AND ' . str_pad((string) $cents, 2, '0', STR_PAD_LEFT) . '/100' : '';

        return strtoupper(implode(' ', $words) . $centsText . ' PESOS ONLY');
    }
}
