<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Ítems vendibles (producto o servicio), con sector para filtrar vistas */
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->enum('type', ['product','service']);
            $table->enum('sector', ['papeleria','impresion','diseno']);

            $table->integer('sale_price')->default(0);
            $table->integer('cost')->default(0);
            $table->string('unit', 30)->nullable();

            $table->string('barcode', 64)->nullable()->unique();
            $table->string('color', 40)->default('N/A');
            $table->string('scan_mode', 10)->nullable();
            $table->unsignedInteger('pack_size')->nullable();
            $table->string('barcode_source', 20)->nullable();
            $table->string('internal_sku', 64)->nullable();

            $table->boolean('featured')->default(false);
            $table->boolean('active')->default(true);

            $table->timestamps();
            $table->index(['sector','type']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('items');
    }
};
