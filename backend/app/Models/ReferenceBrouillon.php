<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferenceBrouillon extends Model
{
    protected $fillable = [
        'deposit_request_id',
        'title',
        'subtitle',
        'authors',
        'category_id',
        'publisher',
        'publication_year',
        'pages',
        'isbn',
        'language',
        'document_type',
        'keywords',
        'abstract',
        'cover_image',
        'file_path',
    ];

    // Relation avec la demande de dépôt
    public function depositRequest(): BelongsTo
    {
        return $this->belongsTo(DepositRequest::class);
    }

    // Relation avec la catégorie
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}