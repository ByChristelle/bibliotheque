<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferenceKeyword extends Model
{
    protected $fillable = ['reference_id', 'keyword'];

    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class);
    }
}
