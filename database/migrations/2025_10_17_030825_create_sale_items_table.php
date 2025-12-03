<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Detalle de líneas por venta */
    public function up(): void {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('description')->nullable();     // para servicios personalizados
            $table->string('category', 30)->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->integer('unit_price')->default(0);
            $table->integer('line_total')->default(0);
            $table->integer('sheets_used')->nullable();

            $table->timestamps();
            $table->index(['sale_id','item_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('sale_items');
    }
};
