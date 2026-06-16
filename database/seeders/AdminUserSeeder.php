<?php

namespace Database\Seeders;

use App\Services\AdminUserProvisioner;
use Illuminate\Database\Seeder;
use Illuminate\Validation\ValidationException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! config('admin.password')) {
            $this->command?->warn('ADMIN_PASSWORD no definido; se omite creación del administrador.');

            return;
        }

        try {
            $user = app(AdminUserProvisioner::class)->ensure();
            $this->command?->info("Administrador listo: {$user->email}");
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $messages) {
                foreach ($messages as $message) {
                    $this->command?->error($message);
                }
            }
        }
    }
}
