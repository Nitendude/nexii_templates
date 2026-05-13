<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecordMonitoringEntry;
use App\Services\RecordMonitoringSyncService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ZipArchive;
use SimpleXMLElement;

class RecordMonitoringController extends Controller
{
    public function __construct(private readonly RecordMonitoringSyncService $recordMonitoringSyncService)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $quickFilter = trim((string) $request->string('filter'));
        $statusFilter = trim((string) $request->string('status'));
        $inChargeFilter = trim((string) $request->string('in_charge'));
        $perPage = 10;

        $baseQuery = RecordMonitoringEntry::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('client_name', 'like', "%{$search}%")
                        ->orWhere('jo_number', 'like', "%{$search}%")
                        ->orWhere('reference_no', 'like', "%{$search}%")
                        ->orWhere('bl_no', 'like', "%{$search}%")
                        ->orWhere('remarks', 'like', "%{$search}%");
                });
            })
            ->when($quickFilter !== '', function ($query) use ($quickFilter) {
                match ($quickFilter) {
                    'receivables' => $query
                        ->where('entry_group', '!=', 'paid')
                        ->where('balance_amount', '>', 0),
                    'cr' => $query->whereNotNull('cr_no')->where('cr_no', '!=', ''),
                    'ar' => $query->whereNotNull('ar_no')->where('ar_no', '!=', ''),
                    'or1' => $query->where(function ($builder) {
                        $builder->where('remarks', 'like', '%OR 1%')
                            ->orWhere('remarks', 'like', '%OR1%')
                            ->orWhere('raw_data', 'like', '%OR 1%')
                            ->orWhere('raw_data', 'like', '%OR1%');
                    }),
                    'or2' => $query->where(function ($builder) {
                        $builder->where('remarks', 'like', '%OR 2%')
                            ->orWhere('remarks', 'like', '%OR2%')
                            ->orWhere('raw_data', 'like', '%OR 2%')
                            ->orWhere('raw_data', 'like', '%OR2%');
                    }),
                    'bi' => $query->where(function ($builder) {
                        $builder->where('reference_no', 'like', '%BS%')
                            ->orWhere('reference_no', 'like', '%SI%')
                            ->orWhere('raw_data', 'like', '%BS #%')
                            ->orWhere('raw_data', 'like', '%SI #%')
                            ->orWhere('raw_data', 'like', '%BILLING AMT%')
                            ->orWhere('raw_data', 'like', '%FINAL BS AMT%');
                    }),
                    'dn' => $query->where(function ($builder) {
                        $builder->where('reference_no', 'like', '%DN%')
                            ->orWhere('raw_data', 'like', '%DN #%')
                            ->orWhere('raw_data', 'like', '%DN#%')
                            ->orWhere('raw_data', 'like', '% DN %');
                    }),
                    'overdue' => null,
                    default => null,
                };
            })
            ->when($statusFilter !== '', function ($query) use ($statusFilter) {
                $query->where('status_as_of', $statusFilter);
            })
            ->when($inChargeFilter !== '', function ($query) use ($inChargeFilter) {
                $query->where('in_charge', $inChargeFilter);
            });

        $entries = (clone $baseQuery)
            ->orderBy('client_name')
            ->orderBy('entry_group')
            ->orderBy('sort_order')
            ->get();

        if ($quickFilter === 'overdue') {
            $entries = $entries->filter(fn (RecordMonitoringEntry $entry) => $this->recordMonitoringSyncService->isOverdue($entry))->values();
        }

        $allClientGroups = $entries
            ->groupBy('client_name')
            ->map(function (Collection $rows, string $client) {
                $active = $rows->where('entry_group', '!=', 'paid')->values();
                $paid = $rows->where('entry_group', 'paid')->values();
                $overdue = $active->filter(fn (RecordMonitoringEntry $row) => $this->recordMonitoringSyncService->isOverdue($row))->values();

                return [
                    'client' => $client,
                    'active_rows' => $active,
                    'paid_rows' => $paid,
                    'active_count' => $active->count(),
                    'paid_count' => $paid->count(),
                    'overdue_count' => $overdue->count(),
                    'billing_total' => (float) $rows->sum('billing_amount'),
                    'payment_total' => (float) $rows->sum('payment_amount'),
                    'advance_total' => (float) $rows->sum('advances_amount'),
                    'balance_total' => (float) $rows->where('entry_group', '!=', 'paid')->sum('balance_amount'),
                ];
            })
            ->sortKeys()
            ->values();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $clientGroups = new LengthAwarePaginator(
            $allClientGroups->forPage($currentPage, $perPage)->values(),
            $allClientGroups->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $totals = [
            'clients' => $allClientGroups->count(),
            'entries' => $entries->count(),
            'active_balance' => (float) $entries->where('entry_group', '!=', 'paid')->sum('balance_amount'),
            'payments' => (float) $entries->sum('payment_amount'),
            'overdue' => $entries->filter(fn (RecordMonitoringEntry $entry) => $this->recordMonitoringSyncService->isOverdue($entry))->count(),
        ];

        $inChargeOptions = RecordMonitoringEntry::query()
            ->whereNotNull('in_charge')
            ->where('in_charge', '!=', '')
            ->orderBy('in_charge')
            ->pluck('in_charge')
            ->unique()
            ->values();

        return view('admin.record-monitoring.index', [
            'clientGroups' => $clientGroups,
            'totals' => $totals,
            'search' => $search,
            'quickFilter' => $quickFilter,
            'statusFilter' => $statusFilter,
            'inChargeFilter' => $inChargeFilter,
            'statusPresets' => RecordMonitoringSyncService::STATUS_PRESETS,
            'inChargeOptions' => $inChargeOptions,
        ]);
    }

    public function create(): View
    {
        return view('admin.record-monitoring.form', [
            'entry' => new RecordMonitoringEntry([
                'entry_group' => 'active',
            ]),
            'formAction' => route('accounting.record-monitoring.store'),
            'formMethod' => 'POST',
            'title' => 'Add Record Monitoring Entry',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        RecordMonitoringEntry::create($this->validatedPayload($request) + [
            'source_type' => 'manual',
        ]);

        return redirect()
            ->route('accounting.record-monitoring.index')
            ->with('status', 'record-monitoring-saved');
    }

    public function edit(RecordMonitoringEntry $recordMonitoringEntry): View
    {
        return view('admin.record-monitoring.form', [
            'entry' => $recordMonitoringEntry,
            'formAction' => route('accounting.record-monitoring.update', $recordMonitoringEntry),
            'formMethod' => 'PUT',
            'title' => 'Edit Record Monitoring Entry',
        ]);
    }

    public function update(Request $request, RecordMonitoringEntry $recordMonitoringEntry): RedirectResponse
    {
        $this->recordMonitoringSyncService->applyManualFollowUp($recordMonitoringEntry, $this->validatedPayload($request));

        return redirect()
            ->route('accounting.record-monitoring.index')
            ->with('status', 'record-monitoring-updated');
    }

    public function quickUpdate(Request $request, RecordMonitoringEntry $recordMonitoringEntry): RedirectResponse
    {
        $validated = $request->validate([
            'payment_amount' => ['nullable', 'numeric'],
            'wht_amount' => ['nullable', 'numeric'],
            'discount_amount' => ['nullable', 'numeric'],
            'cr_no' => ['nullable', 'string', 'max:255'],
            'ar_no' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'status_as_of' => ['nullable', 'string', 'max:255'],
            'entry_group' => ['nullable', 'in:active,paid'],
        ]);

        foreach (['payment_amount', 'wht_amount', 'discount_amount'] as $field) {
            $validated[$field] = (float) ($validated[$field] ?? 0);
        }

        $this->recordMonitoringSyncService->applyManualFollowUp($recordMonitoringEntry, $validated);

        return redirect()
            ->route('accounting.record-monitoring.index', $request->only(['q', 'filter', 'status', 'in_charge', 'page']))
            ->with('status', 'record-monitoring-updated');
    }

    public function importWorkbook(): RedirectResponse
    {
        $path = '/home/apmserver/Downloads/APM 2026 Record Monitoring.xlsx';

        if (!is_file($path)) {
            return redirect()
                ->route('accounting.record-monitoring.index')
                ->withErrors(['import' => 'The workbook was not found at /home/apmserver/Downloads/APM 2026 Record Monitoring.xlsx.']);
        }

        $rows = $this->parseWorkbook($path);

        DB::transaction(function () use ($rows) {
            $seenIds = $this->recordMonitoringSyncService->syncImportedRows($rows);

            RecordMonitoringEntry::query()
                ->where('source_type', 'workbook')
                ->when($seenIds !== [], fn ($query) => $query->whereNotIn('id', $seenIds))
                ->delete();
        });

        return redirect()
            ->route('accounting.record-monitoring.index')
            ->with('status', 'record-monitoring-imported');
    }

    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'sheet_name' => ['nullable', 'string', 'max:255'],
            'section_name' => ['nullable', 'string', 'max:255'],
            'entry_group' => ['required', 'in:active,paid'],
            'in_charge' => ['nullable', 'string', 'max:255'],
            'date_text' => ['nullable', 'string', 'max:255'],
            'jo_number' => ['nullable', 'string', 'max:255'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'billing_amount' => ['nullable', 'numeric'],
            'advances_amount' => ['nullable', 'numeric'],
            'advances_paid_amount' => ['nullable', 'numeric'],
            'payment_amount' => ['nullable', 'numeric'],
            'vat_amount' => ['nullable', 'numeric'],
            'wht_amount' => ['nullable', 'numeric'],
            'rebate_amount' => ['nullable', 'numeric'],
            'discount_amount' => ['nullable', 'numeric'],
            'deducted_amount' => ['nullable', 'numeric'],
            'cr_no' => ['nullable', 'string', 'max:255'],
            'ar_no' => ['nullable', 'string', 'max:255'],
            'bl_no' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'email_sent_on' => ['nullable', 'string', 'max:255'],
            'email_acknowledged' => ['nullable', 'string', 'max:255'],
            'billing_received_on' => ['nullable', 'string', 'max:255'],
            'received_by' => ['nullable', 'string', 'max:255'],
            'status_as_of' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ([
            'billing_amount',
            'advances_amount',
            'advances_paid_amount',
            'payment_amount',
            'vat_amount',
            'wht_amount',
            'rebate_amount',
            'discount_amount',
            'deducted_amount',
        ] as $field) {
            $validated[$field] = (float) ($validated[$field] ?? 0);
        }

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['balance_amount'] = $this->recordMonitoringSyncService->calculateBalance($validated);

        return $validated;
    }

    private function parseWorkbook(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            abort(500, 'Unable to open Record Monitoring workbook.');
        }

        $sharedStrings = $this->sharedStrings($zip);
        [$sheetTargets, $sheetNames] = $this->workbookTargets($zip);
        $imports = [];

        foreach ($sheetNames as $relationId => $sheetName) {
            $normalizedSheetName = Str::upper(trim($sheetName));

            if (in_array($normalizedSheetName, ['SUMMARY', 'BRIGHTON', '(BRIGHTON FOR FINAL)'], true)) {
                continue;
            }

            $target = $sheetTargets[$relationId] ?? null;
            if (!$target) {
                continue;
            }

            $worksheetXml = $zip->getFromName('xl/' . ltrim($target, '/'));
            if ($worksheetXml === false) {
                continue;
            }

            $rows = $this->worksheetRows($worksheetXml, $sharedStrings);
            $imports = array_merge($imports, $this->mapSheetRows($sheetName, $rows));
        }

        $zip->close();

        return $imports;
    }

    private function sharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $shared = [];
        $root = simplexml_load_string($xml);
        if (!$root instanceof SimpleXMLElement) {
            return [];
        }

        $root->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        foreach ($root->xpath('//a:si') ?: [] as $item) {
            $item->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $parts = $item->xpath('.//a:t') ?: [];
            $shared[] = trim(implode('', array_map(fn ($part) => (string) $part, $parts)));
        }

        return $shared;
    }

    private function workbookTargets(ZipArchive $zip): array
    {
        $workbook = simplexml_load_string((string) $zip->getFromName('xl/workbook.xml'));
        $rels = simplexml_load_string((string) $zip->getFromName('xl/_rels/workbook.xml.rels'));

        $sheetNames = [];
        $targets = [];

        if ($workbook instanceof SimpleXMLElement) {
            $workbook->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            foreach ($workbook->xpath('//a:sheets/a:sheet') ?: [] as $sheet) {
                $attributes = $sheet->attributes('r', true);
                $relationId = (string) ($attributes['id'] ?? '');
                $sheetNames[$relationId] = (string) $sheet['name'];
            }
        }

        if ($rels instanceof SimpleXMLElement) {
            foreach ($rels->Relationship ?? [] as $relationship) {
                $id = (string) $relationship['Id'];
                $targets[$id] = (string) $relationship['Target'];
            }
        }

        return [$targets, $sheetNames];
    }

    private function worksheetRows(string $worksheetXml, array $sharedStrings): array
    {
        $root = simplexml_load_string($worksheetXml);
        if (!$root instanceof SimpleXMLElement) {
            return [];
        }

        $root->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rows = [];

        foreach ($root->xpath('//a:sheetData/a:row') ?: [] as $row) {
            $indexed = [];
            foreach ($row->c as $cell) {
                $reference = (string) $cell['r'];
                $columnLetters = preg_replace('/\d+/', '', $reference) ?: 'A';
                $columnIndex = $this->columnLettersToIndex($columnLetters);
                $type = (string) $cell['t'];
                $value = '';

                if (isset($cell->v)) {
                    $value = (string) $cell->v;
                    if ($type === 's') {
                        $value = $sharedStrings[(int) $value] ?? $value;
                    }
                } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                    $value = (string) $cell->is->t;
                }

                $indexed[$columnIndex] = trim($value);
            }

            if ($indexed === []) {
                $rows[] = [];
                continue;
            }

            ksort($indexed);
            $max = max(array_keys($indexed));
            $normalized = [];
            for ($i = 0; $i <= $max; $i++) {
                $normalized[] = $indexed[$i] ?? '';
            }
            $rows[] = $normalized;
        }

        return $rows;
    }

    private function mapSheetRows(string $sheetName, array $rows): array
    {
        $imports = [];
        $currentClient = $sheetName;
        $currentGroup = 'active';
        $currentSection = $sheetName;
        $currentHeaders = [];
        $sortOrder = 0;

        foreach ($rows as $row) {
            $trimmedRow = array_map(fn ($value) => trim((string) $value), $row);
            $nonEmpty = array_values(array_filter($trimmedRow, fn ($value) => $value !== ''));
            if ($nonEmpty === []) {
                continue;
            }

            $first = strtoupper($trimmedRow[0] ?? '');

            if (str_contains($first, 'PAID BILLINGS')) {
                $currentGroup = 'paid';
                $currentSection = 'PAID BILLINGS';
                $currentHeaders = [];
                continue;
            }

            if ($this->isClientHeaderRow($trimmedRow)) {
                $currentClient = trim($trimmedRow[0]) !== '' ? trim($trimmedRow[0]) : $sheetName;
                $currentSection = $sheetName;
                $currentGroup = 'active';
                $currentHeaders = [];
                continue;
            }

            if ($this->isHeaderRow($trimmedRow)) {
                $currentHeaders = $trimmedRow;
                continue;
            }

            if (count($currentHeaders) === 0 || $this->isBalanceRow($trimmedRow)) {
                continue;
            }

            $mapped = $this->mapDataRow(
                sheetName: $sheetName,
                clientName: $currentClient,
                sectionName: $currentSection,
                entryGroup: $currentGroup,
                headers: $currentHeaders,
                row: $trimmedRow,
                sortOrder: ++$sortOrder,
            );

            if ($mapped !== null) {
                $imports[] = $mapped;
            }
        }

        return $imports;
    }

    private function mapDataRow(
        string $sheetName,
        string $clientName,
        string $sectionName,
        string $entryGroup,
        array $headers,
        array $row,
        int $sortOrder,
    ): ?array {
        $data = [
            'client_name' => $clientName,
            'sheet_name' => $sheetName,
            'section_name' => $sectionName,
            'entry_group' => $entryGroup,
            'date_text' => null,
            'jo_number' => null,
            'reference_no' => null,
            'billing_amount' => 0,
            'advances_amount' => 0,
            'advances_paid_amount' => 0,
            'payment_amount' => 0,
            'vat_amount' => 0,
            'wht_amount' => 0,
            'rebate_amount' => 0,
            'discount_amount' => 0,
            'deducted_amount' => 0,
            'balance_amount' => 0,
            'cr_no' => null,
            'ar_no' => null,
            'bl_no' => null,
            'remarks' => null,
            'email_sent_on' => null,
            'email_acknowledged' => null,
            'billing_received_on' => null,
            'received_by' => null,
            'status_as_of' => null,
            'sort_order' => $sortOrder,
            'raw_data' => [],
            'created_at' => now(),
            'updated_at' => now(),
        ];

        foreach ($headers as $index => $header) {
            $value = trim((string) ($row[$index] ?? ''));
            $normalized = $this->normalizeHeader($header);
            if ($normalized === '') {
                continue;
            }

            $data['raw_data'][$header] = $value;

            if ($this->isDateHeader($normalized)) {
                if ($data['date_text'] === null) {
                    $data['date_text'] = $this->normalizeDateValue($value);
                } elseif ($data['email_sent_on'] === null) {
                    $data['email_sent_on'] = $this->normalizeDateValue($value);
                } elseif ($data['billing_received_on'] === null) {
                    $data['billing_received_on'] = $this->normalizeDateValue($value);
                }
                continue;
            }

            match (true) {
                str_contains($normalized, 'j o') => $data['jo_number'] = $value ?: $data['jo_number'],
                str_contains($normalized, 'bs') || str_contains($normalized, 'si') || str_contains($normalized, 'dn') => $data['reference_no'] = $value ?: $data['reference_no'],
                str_contains($normalized, 'final bs amt') || str_contains($normalized, 'billing amt') || $normalized === 'amount' => $data['billing_amount'] = $this->numericValue($value),
                $normalized === 'advances' => $data['advances_amount'] = $this->numericValue($value),
                str_contains($normalized, 'amt paid advances') => $data['advances_paid_amount'] = $this->numericValue($value),
                $normalized === 'payment' => $data['payment_amount'] = $this->numericValue($value),
                $normalized === 'vat' => $data['vat_amount'] = $this->numericValue($value),
                str_contains($normalized, 'w tax') || $normalized === 'wht' => $data['wht_amount'] = $this->numericValue($value),
                $normalized === 'rebate' => $data['rebate_amount'] = $this->numericValue($value),
                $normalized === 'discount' => $data['discount_amount'] = $this->numericValue($value),
                str_contains($normalized, 'balance') => $data['balance_amount'] = $this->numericValue($value),
                str_contains($normalized, 'deducted by brighton') => $data['deducted_amount'] = $this->numericValue($value),
                str_contains($normalized, 'cr no') || $normalized === 'or' || str_contains($normalized, 'or #') => $data['cr_no'] = $value ?: $data['cr_no'],
                str_contains($normalized, 'ar no') || str_contains($normalized, 'ar adv') => $data['ar_no'] = $value ?: $data['ar_no'],
                str_contains($normalized, 'remarks') => $data['remarks'] = $value ?: $data['remarks'],
                str_contains($normalized, 'bl') => $data['bl_no'] = $value ?: $data['bl_no'],
                str_contains($normalized, 'acknowledged') || str_contains($normalized, 'yes or no') => $data['email_acknowledged'] = $value ?: $data['email_acknowledged'],
                str_contains($normalized, 'received') && str_contains($normalized, 'by') => $data['received_by'] = $value ?: $data['received_by'],
                str_contains($normalized, 'status as of') => $data['status_as_of'] = $value ?: $data['status_as_of'],
                str_contains($normalized, 'billing receiving') => $data['billing_received_on'] = $this->normalizeDateValue($value),
                default => null,
            };
        }

        if (
            blank($data['jo_number']) &&
            blank($data['reference_no']) &&
            $data['billing_amount'] == 0.0 &&
            $data['advances_amount'] == 0.0 &&
            $data['payment_amount'] == 0.0 &&
            $data['balance_amount'] == 0.0 &&
            blank($data['remarks']) &&
            blank($data['bl_no'])
        ) {
            return null;
        }

        return $data;
    }

    private function columnLettersToIndex(string $letters): int
    {
        $index = 0;
        foreach (str_split(strtoupper($letters)) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return max(0, $index - 1);
    }

    private function isClientHeaderRow(array $row): bool
    {
        $first = trim((string) ($row[0] ?? ''));
        $joined = strtoupper(implode(' ', array_filter($row)));

        return $first !== '' &&
            (str_contains($joined, 'EMAIL') || str_contains($joined, 'ACKNOWLEDGED')) &&
            !str_contains($joined, 'DATE');
    }

    private function isHeaderRow(array $row): bool
    {
        $joined = $this->normalizeHeader(implode(' ', array_filter($row)));

        return str_contains($joined, 'date') &&
            (str_contains($joined, 'j o') || str_contains($joined, 'jo')) &&
            (str_contains($joined, 'billing amt') || str_contains($joined, 'amount') || str_contains($joined, 'bs'));
    }

    private function isBalanceRow(array $row): bool
    {
        $first = strtoupper(trim((string) ($row[0] ?? '')));
        return $first === 'BALANCE';
    }

    private function normalizeHeader(string $value): string
    {
        $value = Str::lower(trim($value));
        $value = str_replace(['#', '.', ':', '/', '-', '_', '(', ')'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        return trim($value);
    }

    private function isDateHeader(string $normalizedHeader): bool
    {
        return $normalizedHeader === 'date';
    }

    private function numericValue(string $value): float
    {
        $clean = str_replace([',', 'PHP', 'php'], '', trim($value));
        return is_numeric($clean) ? (float) $clean : 0.0;
    }

    private function normalizeDateValue(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (is_numeric($value) && (float) $value > 30000) {
            $serial = (int) round((float) $value);
            $timestamp = ($serial - 25569) * 86400;
            return gmdate('m/d/Y', $timestamp);
        }

        return $value;
    }
}
