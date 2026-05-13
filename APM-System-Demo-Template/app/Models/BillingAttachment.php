<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BillingAttachment extends Model
{
    protected $fillable = [
        'path',
        'filename',
        'mime_type',
        'size',
        'uploaded_by_user_id',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
