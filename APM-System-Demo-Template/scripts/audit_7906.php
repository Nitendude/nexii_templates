<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = App\Models\CashAdvanceRequest::query()
    ->where('user_id', 19)
    ->where('ca_no', '7906')
    ->withCount(['items', 'liquidations'])
    ->orderBy('id')
    ->get(['id', 'ca_no', 'amount', 'created_at', 'status', 'paid_at']);

foreach ($rows as $r) {
    echo "REQ {$r->id} amount={$r->amount} created={$r->created_at} status={$r->status} paid_at={$r->paid_at} items={$r->items_count} liqs={$r->liquidations_count}" . PHP_EOL;
    $items = App\Models\CashAdvanceItem::query()
        ->where('cash_advance_request_id', $r->id)
        ->get(['id', 'jo_number', 'reason', 'amount']);
    foreach ($items as $i) {
        echo "  ITEM {$i->id} jo={$i->jo_number} reason={$i->reason} amount={$i->amount}" . PHP_EOL;
    }
    $liqs = App\Models\CashAdvanceLiquidation::query()
        ->where('cash_advance_request_id', $r->id)
        ->get(['id', 'date', 'ref_no', 'jo_number', 'amount']);
    foreach ($liqs as $l) {
        echo "  LIQ {$l->id} date={$l->date} ref={$l->ref_no} jo={$l->jo_number} amount={$l->amount}" . PHP_EOL;
    }
}
