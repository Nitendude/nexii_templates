<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReimbursableVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_no',
        'status',
        'cancelled_voucher_no',
        'payee',
        'voucher_date',
        'ref_no',
        'total_amount',
        'amount_in_words',
        'prepared_by',
        'approved_by',
        'received_payment',
        'created_by',
        'cancelled_at',
        'cancelled_by',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'total_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(ReimbursableVoucherItem::class)->orderBy('line_no');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
