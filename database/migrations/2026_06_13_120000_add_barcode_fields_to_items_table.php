<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('barcode', 64)->nullable()->unique()->after('unit');
            $table->string('color', 40)->default('N/A')->after('barcode');
            $table->string('scan_mode', 10)->nullable()->after('color');
            $table->unsignedInteger('pack_size')->nullable()->after('scan_mode');
            $table->string('barcode_source', 20)->nullable()->after('pack_size');
            $table->string('internal_sku', 64)->nullable()->after('barcode_source');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'barcode',
                'color',
                'scan_mode',
                'pack_size',
                'barcode_source',
                'internal_sku',
            ]);
        });
    }
};
