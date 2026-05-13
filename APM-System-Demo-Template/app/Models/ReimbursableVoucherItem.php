<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReimbursableVoucherItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reimbursable_voucher_id',
        'line_no',
        'jo_no',
        'client_name',
        'payee',
        'deduct_ca',
        'deduction_type',
        'description',
        'liq_no',
        'rv_cv_no',
        'remarks',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deduct_ca' => 'boolean',
    ];

    public function voucher()
    {
        return $this->belongsTo(ReimbursableVoucher::class, 'reimbursable_voucher_id');
    }
}
