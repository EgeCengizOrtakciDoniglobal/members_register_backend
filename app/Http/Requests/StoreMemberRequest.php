<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
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
            'lastname' => ['required', 'string', 'max:255'],
            'mail' => ['required', 'email', 'max:255', 'unique:members,mail'],
            'tckn' => ['required', 'digits:11', 'unique:members,tckn'],
            'lisanceno' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'in:active,inactive,pending'],
        ];
    }
}
