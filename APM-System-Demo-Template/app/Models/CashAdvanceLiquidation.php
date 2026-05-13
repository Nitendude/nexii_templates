<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CashAdvanceLiquidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_advance_request_id',
        'date',
        'ref_no',
        'jo_number',
        'amount',
        'remarks',
        'receipt_path',
        'receipt_paths',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'receipt_paths' => 'array',
        'approved_at' => 'datetime',
    ];

    public function cashAdvanceRequest()
    {
        return $this->belongsTo(CashAdvanceRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
