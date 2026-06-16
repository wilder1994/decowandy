<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminUserProvisioner
{
    public function ensure(): User
    {
        $name = (string) config('admin.name');
        $email = (string) config('admin.email');
        $password = (string) config('admin.password');

        $validator = Validator::make(
            compact('name', 'email', 'password'),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
            ],
            [
                'password.required' => 'Define ADMIN_PASSWORD en tu archivo .env.',
                'password.min' => 'ADMIN_PASSWORD debe tener al menos 8 caracteres.',
            ]
        );

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->role = User::ROLE_ADMIN;
        $user->can_operate = false;
        $user->can_inventory = false;
        $user->active = true;
        $user->email_verified_at = $user->email_verified_at ?? now();
        $user->password = $password;
        $user->save();

        return $user;
    }
}
