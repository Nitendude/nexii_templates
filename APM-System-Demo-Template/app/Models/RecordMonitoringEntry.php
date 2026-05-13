<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordMonitoringEntry extends Model
{
    protected $fillable = [
        'source_type',
        'source_id',
        'client_name',
        'sheet_name',
        'section_name',
        'entry_group',
        'in_charge',
        'date_text',
        'jo_number',
        'reference_no',
        'billing_amount',
        'advances_amount',
        'advances_paid_amount',
        'payment_amount',
        'vat_amount',
        'wht_amount',
        'rebate_amount',
        'discount_amount',
        'deducted_amount',
        'balance_amount',
        'cr_no',
        'ar_no',
        'bl_no',
        'remarks',
        'email_sent_on',
        'email_acknowledged',
        'billing_received_on',
        'received_by',
        'status_as_of',
        'sort_order',
        'raw_data',
    ];

    protected $casts = [
        'source_id' => 'integer',
        'billing_amount' => 'decimal:2',
        'advances_amount' => 'decimal:2',
        'advances_paid_amount' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'wht_amount' => 'decimal:2',
        'rebate_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'deducted_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'raw_data' => 'array',
    ];
}
