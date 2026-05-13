<?php

declare(strict_types=1);

use App\Models\CashAdvanceLiquidation;
use App\Models\CashAdvanceRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$filePath = '/home/apmserver/Downloads/APM 2026- FORM-CA MONITORING.xlsx';

if (!is_file($filePath)) {
    fwrite(STDERR, "Workbook not found: {$filePath}\n");
    exit(1);
}

/**
 * Sheet title => user name mapping.
 * Keep only high-confidence sheet names to avoid writing to wrong employee.
 */
$sheetUserMap = [
    'ARNOLD' => 'Arnold Jr. Capungcol',
    'AWEL' => 'Awel, Palma',
    'DANDY' => 'Dandy Catubig',
    'DANTE' => 'Dante Deocareza',
    'JETHRO' => 'Jethro Ricardo',
    'JHON RAFAEL' => 'Jhon Rafael Luage',
    'JOHN PHILIP' => 'John Philip Maniego',
    'JOSEPH MASICAT' => 'Joseph Masicat',
    'KRISTINE' => 'Kristine Pasion',
    'LERIC' => 'John Leric Macalalad',
    'MARVIN' => 'Marvin Manalo',
    'MERCY' => 'Mercy Santander',
    'MIKE' => 'Michael Opeda',
    'RAFAEL' => 'Rafael Lazona',
    'RAJ' => 'Stamaria, Raj Marfel',
    'REDILYN' => 'Redilyn Israel',
    'ROY' => 'Abogado, Roy',
    'RYAN' => 'Ryan Velasco',
    'SATURNINO' => 'Saturnino Isog Jr.',
    'WILFREDO' => 'Wilfredo Jr Bacala',
    'WHIN' => 'Velasco, Whin Joyce',
    'JOMS' => 'Cabacang, Jomel',
];

$usersByName = User::query()->get()->keyBy(fn (User $u) => mb_strtolower(trim((string) $u->name)));

$spreadsheet = IOFactory::load($filePath);

$grand = [
    'sheets_processed' => 0,
    'rows_scanned' => 0,
    'ca_created' => 0,
    'ca_reused' => 0,
    'items_created' => 0,
    'liq_created' => 0,
    'request_totals_updated' => 0,
];

$perUser = [];

DB::transaction(function () use (
    $spreadsheet,
    $sheetUserMap,
    $usersByName,
    &$grand,
    &$perUser
): void {
    /** @var Worksheet $sheet */
    foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
        $sheetTitle = strtoupper(trim($sheet->getTitle()));
        if (!array_key_exists($sheetTitle, $sheetUserMap)) {
            continue;
        }

        $userName = $sheetUserMap[$sheetTitle];
        $user = $usersByName->get(mb_strtolower(trim($userName)));
        if (!$user) {
            continue;
        }

        $grand['sheets_processed']++;
        $perUser[$user->name] = $perUser[$user->name] ?? [
            'sheet' => $sheet->getTitle(),
            'rows_scanned' => 0,
            'ca_created' => 0,
            'ca_reused' => 0,
            'items_created' => 0,
            'liq_created' => 0,
            'request_totals_updated' => 0,
        ];

        $currentRequest = null;
        $currentLiqDate = null;
        $touchedRequestIds = [];

        $highestRow = $sheet->getHighestDataRow();
        for ($row = 3; $row <= $highestRow; $row++) {
            $grand['rows_scanned']++;
            $perUser[$user->name]['rows_scanned']++;

            $caDateRaw = trim((string) $sheet->getCell("A{$row}")->getFormattedValue());
            $caNo = trim((string) $sheet->getCell("B{$row}")->getFormattedValue());
            $caField = trim((string) $sheet->getCell("C{$row}")->getFormattedValue());
            $caAmount = parseAmount((string) $sheet->getCell("D{$row}")->getFormattedValue());

            $liqDateRaw = trim((string) $sheet->getCell("E{$row}")->getFormattedValue());
            $refNo = trim((string) $sheet->getCell("F{$row}")->getFormattedValue());
            $liqField = trim((string) $sheet->getCell("G{$row}")->getFormattedValue());
            $liqAmount = parseAmount((string) $sheet->getCell("H{$row}")->getFormattedValue());
            $remarks = trim((string) $sheet->getCell("J{$row}")->getFormattedValue());
            $remarks = $remarks !== '' ? $remarks : null;

            if (
                $caDateRaw === '' && $caNo === '' && $caField === '' && $caAmount <= 0 &&
                $liqDateRaw === '' && $refNo === '' && $liqField === '' && $liqAmount <= 0 &&
                $remarks === null
            ) {
                continue;
            }

            // Recovery for shifted liquidation columns on continuation rows.
            if (
                $currentRequest &&
                $caNo !== '' &&
                $caField !== '' &&
                $caAmount > 0 &&
                $liqDateRaw === '' &&
                $refNo === '' &&
                $liqField === '' &&
                $liqAmount <= 0 &&
                parseDate($caDateRaw) !== null
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

            if ($caNo !== '' && isValidCaNo($caNo)) {
                $parsedCaDate = parseDate($caDateRaw);
                $createdAt = $parsedCaDate ?? parseDate($liqDateRaw) ?? Carbon::now()->startOfDay();

                $requestRecord = CashAdvanceRequest::query()->firstOrCreate(
                    [
                        'user_id' => $user->id,
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
                    $grand['ca_created']++;
                    $perUser[$user->name]['ca_created']++;
                } else {
                    $grand['ca_reused']++;
                    $perUser[$user->name]['ca_reused']++;
                    $updates = [
                        'status' => 'Approved',
                        'approved_at' => $requestRecord->approved_at ?? $createdAt,
                        'paid_at' => $requestRecord->paid_at ?? $createdAt,
                    ];
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
                [$joNumber, $reason] = splitJoAndReason($caField);
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
                    $grand['items_created']++;
                    $perUser[$user->name]['items_created']++;
                }
            }

            if ($currentRequest && $liqAmount > 0) {
                [$liqJoNumber, $liqReason] = splitJoAndReason($liqField);
                $liqDate = parseDate($liqDateRaw)
                    ?? $currentLiqDate
                    ?? parseDate($caDateRaw)
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
                        'status' => 'Approved',
                    ]);
                    $grand['liq_created']++;
                    $perUser[$user->name]['liq_created']++;
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
            $grand['request_totals_updated']++;
            $perUser[$user->name]['request_totals_updated']++;
        }
    }
});

echo "=== CA Monitoring Sync Completed ===\n";
echo json_encode($grand, JSON_PRETTY_PRINT) . "\n\n";
echo "=== Per User ===\n";
foreach ($perUser as $userName => $stats) {
    echo "- {$userName} ({$stats['sheet']}): "
        . "rows={$stats['rows_scanned']}, "
        . "ca_created={$stats['ca_created']}, "
        . "ca_reused={$stats['ca_reused']}, "
        . "items_created={$stats['items_created']}, "
        . "liq_created={$stats['liq_created']}, "
        . "request_totals_updated={$stats['request_totals_updated']}\n";
}

function parseAmount(string $value): float
{
    $value = trim($value);
    if ($value === '') {
        return 0.0;
    }

    $negative = false;
    if (preg_match('/^\((.*)\)$/', $value, $m)) {
        $negative = true;
        $value = $m[1];
    }

    $normalized = str_replace([',', ' '], '', $value);
    if ($normalized === '' || !is_numeric($normalized)) {
        return 0.0;
    }

    $amount = (float) $normalized;
    return $negative ? -$amount : $amount;
}

function parseDate(string $value): ?Carbon
{
    $value = trim($value);
    if ($value === '') {
        return null;
    }

    $formats = ['n/j/Y', 'm/d/Y', 'Y-m-d', 'n/j/y', 'm/d/y'];
    foreach ($formats as $format) {
        try {
            $dt = Carbon::createFromFormat($format, $value);
            if ($dt !== false) {
                return $dt->startOfDay();
            }
        } catch (Throwable) {
        }
    }

    try {
        return Carbon::parse($value)->startOfDay();
    } catch (Throwable) {
        return null;
    }
}

function isValidCaNo(string $value): bool
{
    $value = trim($value);
    if ($value === '') {
        return false;
    }

    if (preg_match('/^\d+\.\d+$/', $value)) {
        return false;
    }

    return preg_match('/\d/', $value) === 1;
}

function splitJoAndReason(string $field): array
{
    $field = trim($field);
    if ($field === '') {
        return [null, null];
    }

    if (preg_match('/^\d+$/', $field)) {
        return [$field, null];
    }

    if (preg_match('/([A-Z]{2,4}\s*[-\/]\s*\d{3,6}|\d{4,6})/i', $field, $m)) {
        return [trim($m[1]), null];
    }

    return [null, $field];
}

