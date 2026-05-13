<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DebitCreditNote extends Model
{
    protected $fillable = [
        'note_no',
        'job_order_id',
        'note_type',
        'note_date',
        'amount',
        'description',
        'remarks',
        'data',
        'created_by_user_id',
    ];

    protected $casts = [
        'note_date' => 'date',
        'amount' => 'decimal:2',
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
