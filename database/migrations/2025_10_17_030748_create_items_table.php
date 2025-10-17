<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Ítems vendibles (producto o servicio), con sector para filtrar vistas */
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');                // nombre del ítem (código/sku opcional más adelante)
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->enum('type', ['product','service']); // producto o servicio
            $table->enum('sector', ['papeleria','impresion','diseno']);

            $table->decimal('sale_price', 12, 2)->default(0); // precio de venta base
            $table->decimal('cost', 12, 2)->default(0);       // costo (promedio) para margen
            $table->integer('stock')->default(0);            // solo aplica si type=product
            $table->integer('min_stock')->default(0);        // alerta de stock bajo
            $table->string('unit', 30)->nullable();          // hoja, unidad, paquete, servicio

            $table->boolean('featured')->default(false);     // destacar en landing
            $table->boolean('active')->default(true);

            $table->timestamps();
            $table->index(['sector','type']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('items');
    }
};
