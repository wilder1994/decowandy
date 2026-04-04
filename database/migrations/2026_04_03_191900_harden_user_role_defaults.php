<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::table('users')
            ->whereNull('role')
            ->orWhere('role', '')
            ->update(['role' => User::ROLE_OPERATOR]);

        DB::table('users')
            ->whereNotIn('role', User::allowedRoles())
            ->update(['role' => User::ROLE_OPERATOR]);

        DB::table('users')
            ->whereNull('active')
            ->update(['active' => true]);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default(User::ROLE_OPERATOR)->change();
            $table->boolean('active')->default(true)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default(User::ROLE_ADMIN)->change();
            $table->boolean('active')->default(true)->change();
        });
    }
};
