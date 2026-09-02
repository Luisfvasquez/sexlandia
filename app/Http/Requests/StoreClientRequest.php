<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\.\-\']+$/'],
            'identification' => ['required', 'string', 'regex:/^[a-zA-Z0-9\-]+$/', 'unique:clients,identification'], // Cédula o RIF
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
            'address' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.\,\#\-\/°]+$/'],
            'create_account' => 'nullable|boolean',
        ];

        if ($this->boolean('create_account')) {
            $rules['email'] = 'required|email|unique:users,email';
        } else {
            $rules['email'] = 'nullable|email|unique:clients,email';
        }

        return $rules;
    }
}
