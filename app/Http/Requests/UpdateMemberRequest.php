<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
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
        $memberId = $this->route('member')?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'lastname' => ['sometimes', 'required', 'string', 'max:255'],
            'mail' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('members', 'mail')->ignore($memberId)],
            'tckn' => ['sometimes', 'required', 'digits:11', Rule::unique('members', 'tckn')->ignore($memberId)],
            'lisanceno' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'in:active,inactive,pending'],
        ];
    }
}
