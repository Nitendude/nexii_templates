<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CashAdvanceLiquidation;
use App\Models\CashAdvanceRequest;
use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    $userId = 19;

    $master = CashAdvanceRequest::query()
        ->where('user_id', $userId)
        ->where('ca_no', '7906')
        ->orderByDesc('id')
        ->first();

    if (!$master) {
        $master = CashAdvanceRequest::query()->create([
            'user_id' => $userId,
            'ca_no' => '7906',
            'amount' => 0,
            'status' => 'Approved',
            'approved_at' => '2025-01-27 00:00:00',
            'paid_at' => '2025-01-27 00:00:00',
            'created_at' => '2025-01-27 00:00:00',
            'updated_at' => now(),
        ]);
    } else {
        $master->update([
            'status' => 'Approved',
            'approved_at' => $master->approved_at ?? '2025-01-27 00:00:00',
            'paid_at' => $master->paid_at ?? '2025-01-27 00:00:00',
        ]);

        DB::table('cash_advance_requests')
            ->where('id', $master->id)
            ->update([
                'created_at' => '2025-01-27 00:00:00',
                'updated_at' => now(),
            ]);
    }

    $items = [
        ['jo_number' => '12250', 'reason' => null, 'amount' => 5000.00],
        ['jo_number' => null, 'reason' => 'TRANSPO', 'amount' => 1500.00],
    ];

    foreach ($items as $row) {
        $exists = $master->items()
            ->where('jo_number', $row['jo_number'])
            ->where('reason', $row['reason'])
            ->where('amount', $row['amount'])
            ->exists();

        if (!$exists) {
            $master->items()->create($row);
        }
    }

    $liqs = [
        ['date' => '2025-02-03', 'ref_no' => '54507', 'jo_number' => '12197', 'amount' => 60.00, 'remarks' => null],
        ['date' => '2025-02-03', 'ref_no' => '54558', 'jo_number' => '12217', 'amount' => 1000.00, 'remarks' => null],
        ['date' => '2025-02-03', 'ref_no' => '54543', 'jo_number' => '12151', 'amount' => 110.00, 'remarks' => null],
        ['date' => '2025-02-03', 'ref_no' => '54544', 'jo_number' => '12152', 'amount' => 110.00, 'remarks' => null],
        ['date' => '2025-02-03', 'ref_no' => '11974', 'jo_number' => null, 'amount' => 1490.00, 'remarks' => null],
        ['date' => '2025-02-03', 'ref_no' => '11143', 'jo_number' => null, 'amount' => 600.00, 'remarks' => null],
        ['date' => '2025-02-03', 'ref_no' => '11144', 'jo_number' => null, 'amount' => 780.00, 'remarks' => 'LOAD FEB 25'],
        ['date' => '2025-02-05', 'ref_no' => '54627', 'jo_number' => '12278', 'amount' => 100.00, 'remarks' => null],
        ['date' => '2025-02-05', 'ref_no' => '54628', 'jo_number' => '12241', 'amount' => 50.00, 'remarks' => null],
        ['date' => '2025-02-06', 'ref_no' => '54645', 'jo_number' => '12319', 'amount' => 200.00, 'remarks' => null],
        ['date' => '2025-02-06', 'ref_no' => '11975', 'jo_number' => null, 'amount' => 845.00, 'remarks' => null],
        ['date' => '2025-02-11', 'ref_no' => '10677', 'jo_number' => null, 'amount' => 784.44, 'remarks' => null],
        ['date' => '2025-02-11', 'ref_no' => '54704', 'jo_number' => '12266', 'amount' => 120.00, 'remarks' => null],
        ['date' => '2025-02-11', 'ref_no' => '54705', 'jo_number' => '12193', 'amount' => 110.00, 'remarks' => null],
    ];

    foreach ($liqs as $row) {
        $exists = CashAdvanceLiquidation::query()
            ->where('cash_advance_request_id', $master->id)
            ->whereDate('date', $row['date'])
            ->where('ref_no', $row['ref_no'])
            ->where('jo_number', $row['jo_number'])
            ->where('amount', $row['amount'])
            ->exists();

        if (!$exists) {
            CashAdvanceLiquidation::query()->create([
                'cash_advance_request_id' => $master->id,
                'date' => $row['date'],
                'ref_no' => $row['ref_no'],
                'jo_number' => $row['jo_number'],
                'amount' => $row['amount'],
                'remarks' => $row['remarks'],
            ]);
        }
    }

    $accidentalCaNos = ['54543', '54544', '11974', '11143', '11144', '54627', '54628', '54645', '11975', '10677', '54704', '54705', '54558'];

    $bad = CashAdvanceRequest::query()
        ->where('user_id', $userId)
        ->where('id', '!=', $master->id)
        ->whereIn('ca_no', $accidentalCaNos)
        ->withCount(['items', 'liquidations'])
        ->get();

    foreach ($bad as $b) {
        if ((int) $b->liquidations_count === 0) {
            $b->items()->delete();
            $b->delete();
        }
    }

    $master->refresh();
    $total = $master->items()->sum('amount');
    $master->update(['amount' => $total]);

    echo "MASTER={$master->id};ITEMS=" . $master->items()->count() . ";LIQS=" . $master->liquidations()->count() . ";AMOUNT={$total}" . PHP_EOL;
});
