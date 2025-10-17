<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Agrega el campo 'role' para manejar permisos básicos */
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->after('password'); // admin, operator, inventory
            $table->boolean('active')->default(true)->after('role');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role','active']);
        });
    }
};

