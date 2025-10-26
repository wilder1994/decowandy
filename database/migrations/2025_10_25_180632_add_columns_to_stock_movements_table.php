<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movements', 'reason')) {
                $table->string('reason')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('stock_movements', 'related_id')) {
                $table->unsignedBigInteger('related_id')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('stock_movements', 'unit_cost')) {
                $table->integer('unit_cost')->nullable()->after('related_id');
            }
            // Si la columna 'type' no existe, crearla (in|out|adjust)
            if (!Schema::hasColumn('stock_movements', 'type')) {
                $table->string('type', 10)->default('in')->after('item_id');
            }
        });
    }


    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            if (Schema::hasColumn('stock_movements', 'unit_cost')) {
                $table->dropColumn('unit_cost');
            }
            if (Schema::hasColumn('stock_movements', 'related_id')) {
                $table->dropColumn('related_id');
            }
            if (Schema::hasColumn('stock_movements', 'reason')) {
                $table->dropColumn('reason');
            }
            if (Schema::hasColumn('stock_movements', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
