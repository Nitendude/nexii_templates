<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'method',
        'route_name',
        'module',
        'url',
        'ip_address',
        'user_agent',
        'response_status',
        'request_payload',
        'route_parameters',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'route_parameters' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
