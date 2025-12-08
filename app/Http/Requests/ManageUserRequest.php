<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManageUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'role' => ['nullable', 'string', 'max:50'],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string',
                'min:6',
            ],
        ];
    }
}
