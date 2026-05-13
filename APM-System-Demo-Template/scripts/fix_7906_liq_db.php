<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$before = DB::table('cash_advance_liquidations')
    ->where('id', 46)
    ->first(['id', 'ref_no', 'jo_number', 'amount']);

if (!$before) {
    echo "NO_LIQ_46" . PHP_EOL;
    exit(0);
}

DB::table('cash_advance_liquidations')
    ->where('id', 46)
    ->update([
        'ref_no' => '54558',
        'jo_number' => '12217',
        'amount' => 1000.00,
        'updated_at' => now(),
    ]);

$after = DB::table('cash_advance_liquidations')
    ->where('id', 46)
    ->first(['id', 'ref_no', 'jo_number', 'amount']);

echo "BEFORE ref={$before->ref_no} jo={$before->jo_number} amount={$before->amount}" . PHP_EOL;
echo "AFTER  ref={$after->ref_no} jo={$after->jo_number} amount={$after->amount}" . PHP_EOL;
