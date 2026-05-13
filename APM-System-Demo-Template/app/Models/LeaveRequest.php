<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function workingDaysBetween(Carbon|string $startDate, Carbon|string $endDate): int
    {
        $start = $startDate instanceof Carbon ? $startDate->copy()->startOfDay() : Carbon::parse($startDate)->startOfDay();
        $end = $endDate instanceof Carbon ? $endDate->copy()->startOfDay() : Carbon::parse($endDate)->startOfDay();

        if ($start->greaterThan($end)) {
            return 0;
        }

        $days = 0;
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            if (!$cursor->isWeekend()) {
                $days++;
            }

            $cursor->addDay();
        }

        return $days;
    }

    public function workingDays(): int
    {
        return self::workingDaysBetween($this->start_date, $this->end_date);
    }
}
