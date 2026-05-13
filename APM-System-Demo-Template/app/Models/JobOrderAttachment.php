<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_order_id',
        'filename',
        'path',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class);
    }
}
