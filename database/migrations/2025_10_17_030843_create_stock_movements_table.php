<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Movimientos de inventario (entrada/salida) por venta, compra o ajuste */
    public function up(): void {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('type', ['in','out']);
            $table->integer('quantity')->default(0);
            $table->string('reason', 30)->default('sale');
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->integer('unit_cost')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['item_id','type','reason']);
            $table->index(['sale_id','purchase_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('stock_movements');
    }
};
