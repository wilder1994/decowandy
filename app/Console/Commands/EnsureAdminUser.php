<?php

namespace App\Console\Commands;

use App\Services\AdminUserProvisioner;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class EnsureAdminUser extends Command
{
    protected $signature = 'decowandy:ensure-admin';

    protected $description = 'Crea o actualiza el usuario administrador definido en .env';

    public function handle(AdminUserProvisioner $provisioner): int
    {
        try {
            $user = $provisioner->ensure();
            $this->info("Administrador listo: {$user->email}");

            return self::SUCCESS;
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $messages) {
                foreach ($messages as $message) {
                    $this->error($message);
                }
            }

            return self::FAILURE;
        }
    }
}
