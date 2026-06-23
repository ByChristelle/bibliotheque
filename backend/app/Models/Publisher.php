<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publisher extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'country', 'website'];
    protected $dates = ['deleted_at'];

    public function references(): HasMany
    {
        return $this->hasMany(Reference::class);
    }
}
