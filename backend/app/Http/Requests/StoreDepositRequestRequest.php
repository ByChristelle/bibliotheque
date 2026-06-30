<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDepositRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'proposed_file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'], // 20Mo max

             // Champs du brouillon de référence
            'subtitle'         => ['nullable', 'string', 'max:255'],
            'authors'          => ['required', 'string', 'max:255'],
            'category_id'      => ['required', 'exists:categories,id'],
            'publisher'        => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'pages'            => ['nullable', 'integer', 'min:1'],
            'isbn'             => ['nullable', 'string', 'max:20'],
            'language'         => ['required', 'string', 'in:fr,en,autre'],
            'document_type'    => ['required', 'string', 'in:livre,memoire,these,article,revue,rapport,guide,autre'],
            'keywords'         => ['nullable', 'string', 'max:500'],
            'abstract'         => ['nullable', 'string'],
            'cover_image'      => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:5120'], // 5Mo max
            'file'             => ['nullable', 'file', 'mimes:pdf', 'max:20480'], // 20Mo max
        ];
    }
}
