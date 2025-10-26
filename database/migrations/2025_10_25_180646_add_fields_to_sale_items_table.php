<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'category')) {
                $table->string('category', 30)->nullable()->after('item_id');
            }
            if (!Schema::hasColumn('sale_items', 'unit_price')) {
                $table->integer('unit_price')->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('sale_items', 'line_total')) {
                $table->integer('line_total')->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('sale_items', 'sheets_used')) {
                $table->integer('sheets_used')->nullable()->after('line_total');
            }
        });
    }


    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            foreach (['category', 'unit_price', 'line_total', 'sheets_used'] as $col) {
                if (Schema::hasColumn('sale_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
