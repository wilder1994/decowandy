<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Movimientos de inventario (entrada/salida) por venta, compra o ajuste */
    public function up(): void {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnUpdate();
            $table->enum('type', ['in','out']);                       // entrada | salida
            $table->decimal('quantity', 12, 2)->default(0);
            $table->enum('reason', ['sale','purchase','adjustment']); // motivo
            $table->unsignedBigInteger('ref_id')->nullable();         // id de sales/purchases si aplica
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['item_id','type','reason']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('stock_movements');
    }
};

