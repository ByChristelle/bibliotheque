<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepositRequest extends Model
{
    use SoftDeletes;

    protected $fillable = ['applicant_id', 'assigned_manager_id', 'title', 'description', 'proposed_file', 'status'];
    protected $dates = ['deleted_at'];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function assignedManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_manager_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(DepositRequestReview::class);
    }
}
