<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BillingStatement extends Model
{
    protected $fillable = [
        'statement_no',
        'document_type',
        'job_order_id',
        'created_by_user_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(BillingAttachment::class, 'attachable');
    }
}
