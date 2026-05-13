<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'basic_salary',
        'allowances_total',
        'deductions_total',
        'cash_advance_deduction',
        'pagibig_contribution',
        'philhealth_contribution',
        'sss_contribution',
        'net_pay',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'basic_salary' => 'decimal:2',
        'allowances_total' => 'decimal:2',
        'deductions_total' => 'decimal:2',
        'cash_advance_deduction' => 'decimal:2',
        'pagibig_contribution' => 'decimal:2',
        'philhealth_contribution' => 'decimal:2',
        'sss_contribution' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
