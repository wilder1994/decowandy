<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
        $table->string('product_name'); // texto libre
        $table->integer('quantity');
        $table->integer('total_cost'); // costo total facturado por la línea
        $table->integer('unit_cost'); // total_cost / quantity
        $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete(); // si corresponde a un item del POS
        $table->timestamps();
        $table->index('purchase_id');
        });
    }


    public function down(): void
    {
    Schema::dropIfExists('purchase_items');
    }
};