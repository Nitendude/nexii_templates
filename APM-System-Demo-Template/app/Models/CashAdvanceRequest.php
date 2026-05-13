<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAdvanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ca_no',
        'amount',
        'purpose',
        'status',
        'approved_by',
        'approved_at',
        'paid_at',
        'paid_proof_path',
        'paid_by',
        'personal_paid_amount',
        'is_personal',
        'salary_deduction_terms',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'personal_paid_amount' => 'decimal:2',
        'is_personal' => 'boolean',
        'salary_deduction_terms' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(CashAdvanceItem::class);
    }

    public function liquidations()
    {
        return $this->hasMany(CashAdvanceLiquidation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
