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
            'amount' => 6500.00,
            'status' => 'Approved',
            'approved_at' => '2025-01-27 00:00:00',
            'paid_at' => '2025-01-27 00:00:00',
            'created_at' => '2025-01-27 00:00:00',
            'updated_at' => now(),
        ]);
    } else {
        $master->update([
            'status' => 'Approved',
            'amount' => 6500.00,
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

    // Reset items to match ledger breakdown
    $master->items()->delete();
    $master->items()->create(['jo_number' => '12250', 'reason' => null, 'amount' => 5000.00]);
    $master->items()->create(['jo_number' => null, 'reason' => 'TRANSPO', 'amount' => 1500.00]);

    // Reset liquidations to match ledger breakdown
    CashAdvanceLiquidation::query()->where('cash_advance_request_id', $master->id)->delete();
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

    foreach ($liqs as $liq) {
        CashAdvanceLiquidation::query()->create([
            'cash_advance_request_id' => $master->id,
            'date' => $liq['date'],
            'ref_no' => $liq['ref_no'],
            'jo_number' => $liq['jo_number'],
            'amount' => $liq['amount'],
            'remarks' => $liq['remarks'],
        ]);
    }

    echo "MASTER={$master->id};ITEMS={$master->items()->count()};LIQS={$master->liquidations()->count()};AMOUNT=6500.00" . PHP_EOL;
});
