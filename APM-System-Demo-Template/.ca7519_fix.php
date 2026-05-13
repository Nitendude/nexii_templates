
<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CashAdvanceRequest;
use App\Models\CashAdvanceLiquidation;
use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    $userId = 19;
    $req = CashAdvanceRequest::query()
        ->where('user_id', $userId)
        ->where('ca_no', '7519')
        ->orderByDesc('id')
        ->first();

    if (!$req) {
        echo "NO_CA_7519\n";
        return;
    }

    $target = [
        ['date' => '2024-12-18', 'ref_no' => '53745', 'jo_number' => '12016', 'amount' => 10300.00, 'remarks' => 'DTI PERMIT / TWOY'],
        ['date' => '2025-12-18', 'ref_no' => '53719', 'jo_number' => '12104', 'amount' => 400.00, 'remarks' => null],
        ['date' => '2024-12-18', 'ref_no' => '53744', 'jo_number' => '12067', 'amount' => 100.00, 'remarks' => null],
        ['date' => '2024-12-18', 'ref_no' => '53713', 'jo_number' => '12008', 'amount' => 130.00, 'remarks' => null],
    ];

    foreach ($target as $row) {
        $exists = CashAdvanceLiquidation::query()
            ->where('cash_advance_request_id', $req->id)
            ->whereDate('date', $row['date'])
            ->where('ref_no', $row['ref_no'])
            ->where('jo_number', $row['jo_number'])
            ->where('amount', $row['amount'])
            ->exists();

        if (!$exists) {
            CashAdvanceLiquidation::query()->create([
                'cash_advance_request_id' => $req->id,
                'date' => $row['date'],
                'ref_no' => $row['ref_no'],
                'jo_number' => $row['jo_number'],
                'amount' => $row['amount'],
                'remarks' => $row['remarks'],
            ]);
        }
    }

    // Remove erroneous CA requests accidentally created from liquidation ref numbers.
    $badCaNos = ['53719', '53744', '53713'];
    $badRequests = CashAdvanceRequest::query()
        ->where('user_id', $userId)
        ->whereIn('ca_no', $badCaNos)
        ->withCount('liquidations')
        ->get();

    foreach ($badRequests as $bad) {
        if ((int) $bad->liquidations_count === 0) {
            $bad->items()->delete();
            $bad->delete();
        }
    }

    $req->refresh();
    echo "CA_7519_REQ_ID={$req->id};LIQ_COUNT=" . CashAdvanceLiquidation::query()->where('cash_advance_request_id', $req->id)->count() . "\n";
});
