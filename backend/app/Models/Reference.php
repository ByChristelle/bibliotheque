<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reference extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'subtitle', 'abstract', 'isbn', 'publication_year',
        'language', 'document_type', 'category_id', 'publisher_id',
        'uploaded_by', 'cover_image', 'file_path', 'pages',
        'download_count', 'view_count', 'status'
    ];
    protected $dates = ['deleted_at'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'reference_author');
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(ReferenceKeyword::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }
}
