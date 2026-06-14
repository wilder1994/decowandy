<?php

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
            ->update(['role' => 'operator']);

        DB::table('users')
            ->whereNotIn('role', ['admin', 'operator', 'inventory'])
            ->update(['role' => 'operator']);

        DB::table('users')
            ->whereNull('active')
            ->update(['active' => true]);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('operator')->change();
            $table->boolean('active')->default(true)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->change();
            $table->boolean('active')->default(true)->change();
        });
    }
};
