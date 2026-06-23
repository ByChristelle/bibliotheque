<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepositRequestReview extends Model
{
    public $timestamps = false;

    protected $fillable = ['deposit_request_id', 'reviewer_id', 'reviewer_role', 'decision', 'justification', 'created_at'];

    public function depositRequest(): BelongsTo
    {
        return $this->belongsTo(DepositRequest::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
