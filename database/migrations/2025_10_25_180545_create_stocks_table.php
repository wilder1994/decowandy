<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
        $table->integer('quantity')->default(0); // existencia actual
        $table->integer('min_threshold')->nullable();
        $table->timestamps();
        $table->unique('item_id');
        });
    }


    public function down(): void
    {
    Schema::dropIfExists('stocks');
    }
};