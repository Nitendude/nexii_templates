<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$liq = App\Models\CashAdvanceLiquidation::query()->find(46);
if (!$liq) {
    echo "NO_LIQ_46" . PHP_EOL;
    exit(0);
}

$liq->update([
    'ref_no' => '54558',
    'jo_number' => '12217',
    'amount' => 1000.00,
]);

echo "UPDATED_LIQ_46" . PHP_EOL;
