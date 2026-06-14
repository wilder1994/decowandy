<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

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
            'role' => ['required', 'string', Rule::in(User::accountTypes())],
            'can_operate' => ['sometimes', 'boolean'],
            'can_inventory' => ['sometimes', 'boolean'],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->input('role') !== User::ROLE_STAFF) {
                return;
            }

            if (! $this->boolean('can_operate') && ! $this->boolean('can_inventory')) {
                $validator->errors()->add(
                    'can_operate',
                    'Selecciona al menos un módulo para el personal.'
                );
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function accountData(): array
    {
        $data = $this->validated();

        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ?? '',
            'role' => $data['role'],
            'can_operate' => $this->boolean('can_operate'),
            'can_inventory' => $this->boolean('can_inventory'),
        ];
    }
}
