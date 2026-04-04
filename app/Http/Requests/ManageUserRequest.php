<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ManageUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage-users');
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
            'role' => ['required', 'string', Rule::in(User::allowedRoles())],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }
}
