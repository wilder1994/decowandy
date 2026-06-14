<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class EnsureAdminUser extends Command
{
    protected $signature = 'decowandy:ensure-admin';

    protected $description = 'Crea o actualiza el usuario administrador definido en .env';

    public function handle(): int
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
                'password.required' => 'Define ADMIN_PASSWORD en tu archivo .env antes de ejecutar este comando.',
                'password.min' => 'ADMIN_PASSWORD debe tener al menos 8 caracteres.',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
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

        $this->info("Administrador listo: {$user->email}");

        return self::SUCCESS;
    }
}
