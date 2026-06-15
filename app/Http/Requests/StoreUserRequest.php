<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mail' => ['required', 'email', 'max:255', 'unique:users,mail'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['sometimes', 'in:admin,user'],
            'status' => ['sometimes', 'in:active,inactive,pending'],
        ];
    }
}
