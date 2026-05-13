<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position',
        'employment_type',
        'department',
        'date_joined',
    ];

    protected $casts = [
        'date_joined' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
