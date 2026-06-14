<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_operate')->default(false)->after('role');
            $table->boolean('can_inventory')->default(false)->after('can_operate');
        });

        DB::table('users')
            ->where('role', 'operator')
            ->update([
                'role' => 'staff',
                'can_operate' => true,
                'can_inventory' => false,
            ]);

        DB::table('users')
            ->where('role', 'inventory')
            ->update([
                'role' => 'staff',
                'can_operate' => false,
                'can_inventory' => true,
            ]);

        DB::table('users')
            ->where('role', 'admin')
            ->update([
                'can_operate' => false,
                'can_inventory' => false,
            ]);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'staff')
            ->where('can_operate', true)
            ->where('can_inventory', false)
            ->update(['role' => 'operator']);

        DB::table('users')
            ->where('role', 'staff')
            ->where('can_operate', false)
            ->where('can_inventory', true)
            ->update(['role' => 'inventory']);

        DB::table('users')
            ->where('role', 'staff')
            ->where('can_operate', true)
            ->where('can_inventory', true)
            ->update(['role' => 'operator']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['can_operate', 'can_inventory']);
        });
    }
};
