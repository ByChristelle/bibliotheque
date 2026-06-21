<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUsersRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'role'       => ['required', 'string', 'in:admin,responsable_rh,responsable_demande,user'],
            'password'   => ['required', 'string', 'min:8'],
            // 'avatar'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Max 2Mo
      'avatar'     => ['nullable'],
        ];
    }
}
