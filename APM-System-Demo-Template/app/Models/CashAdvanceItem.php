<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAdvanceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_advance_request_id',
        'jo_number',
        'reason',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function request()
    {
        return $this->belongsTo(CashAdvanceRequest::class, 'cash_advance_request_id');
    }
}
