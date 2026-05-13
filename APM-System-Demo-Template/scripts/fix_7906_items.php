<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$req = DB::table('cash_advance_requests')
    ->where('user_id', 19)
    ->where('ca_no', '7906')
    ->orderByDesc('id')
    ->first();

if (!$req) {
    echo "NO_REQ" . PHP_EOL;
    exit(0);
}

DB::transaction(function () use ($req) {
    DB::table('cash_advance_items')
        ->where('cash_advance_request_id', $req->id)
        ->delete();

    DB::table('cash_advance_items')->insert([
        [
            'cash_advance_request_id' => $req->id,
            'jo_number' => '12250',
            'reason' => null,
            'amount' => 5000.00,
            'remarks' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'cash_advance_request_id' => $req->id,
            'jo_number' => null,
            'reason' => 'TRANSPO',
            'amount' => 1500.00,
            'remarks' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    DB::table('cash_advance_requests')
        ->where('id', $req->id)
        ->update([
            'amount' => 6500.00,
            'updated_at' => now(),
        ]);
});

$itemCount = DB::table('cash_advance_items')
    ->where('cash_advance_request_id', $req->id)
    ->count();
$liqCount = DB::table('cash_advance_liquidations')
    ->where('cash_advance_request_id', $req->id)
    ->count();
$amount = DB::table('cash_advance_requests')
    ->where('id', $req->id)
    ->value('amount');

echo "REQ_ID={$req->id};ITEMS={$itemCount};LIQS={$liqCount};AMOUNT={$amount}" . PHP_EOL;
