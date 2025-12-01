<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('items', 'stock') || Schema::hasColumn('items', 'min_stock')) {
            Schema::table('items', function (Blueprint $table) {
                if (Schema::hasColumn('items', 'stock')) {
                    $table->dropColumn('stock');
                }
                if (Schema::hasColumn('items', 'min_stock')) {
                    $table->dropColumn('min_stock');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'stock')) {
                $table->integer('stock')->default(0)->after('cost');
            }
            if (!Schema::hasColumn('items', 'min_stock')) {
                $table->integer('min_stock')->default(0)->after('stock');
            }
        });
    }
};
