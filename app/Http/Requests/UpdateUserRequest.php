<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'mail' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'mail')->ignore($userId)],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
            'role' => ['sometimes', 'in:admin,user'],
            'status' => ['sometimes', 'in:active,inactive,pending'],
        ];
    }
}
