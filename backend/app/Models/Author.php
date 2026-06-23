<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    use SoftDeletes;

    protected $fillable = ['first_name', 'last_name', 'biography', 'nationality', 'birth_date', 'death_date'];
    protected $dates = ['deleted_at', 'birth_date', 'death_date'];

    public function references(): BelongsToMany
    {
        return $this->belongsToMany(Reference::class, 'reference_author');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
