<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DepositRequest extends Model
{
    // use SoftDeletes;

    protected $fillable = ['applicant_id', 'assigned_manager_id', 'title', 'description', 'proposed_file', 'status', 'justification'];
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

     // Nouvelle relation : un brouillon de référence par demande
    public function referenceBrouillon(): HasOne
    {
        return $this->hasOne(ReferenceBrouillon::class);
    }

    // Relation pour les logs d'activité
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'target_id')->where('target_table', 'deposit_requests')->latest();
    }
}
