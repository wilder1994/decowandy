<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Crea el administrador definido en .env (ADMIN_NAME, ADMIN_EMAIL, ADMIN_PASSWORD).
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
    }
}
