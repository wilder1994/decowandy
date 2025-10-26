<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('catalog_items', function (Blueprint $table) {
        $table->id();
        $table->string('category', 30); // Papelería | Impresión | Diseño (validado por Request/UI)
        $table->string('title');
        $table->text('description')->nullable();
        $table->integer('price')->nullable(); // COP enteros
        $table->boolean('show_price')->default(true);
        $table->boolean('visible')->default(true);
        $table->boolean('featured')->default(false);
        $table->integer('sort_order')->default(0);
        $table->string('image_path')->nullable();
        $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
        $table->timestamps();
        $table->index(['category', 'visible', 'sort_order']);
        });
    }


    public function down(): void
    {
    Schema::dropIfExists('catalog_items');
    }
};