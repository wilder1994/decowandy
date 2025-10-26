<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
        $table->id();
        $table->string('category', 30); // Diseño | Papelería | Impresión
        $table->date('date');
        $table->string('supplier')->nullable();
        $table->string('note')->nullable();
        $table->boolean('to_inventory')->default(false); // Agregar al inventario
        $table->integer('total')->default(0); // suma de líneas
        $table->timestamps();
        $table->index(['category', 'date']);
        });
    }


    public function down(): void
    {
    Schema::dropIfExists('purchases');
    }
};