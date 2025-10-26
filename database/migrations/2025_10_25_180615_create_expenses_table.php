<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->date('date');
        $table->string('concept');
        $table->string('category'); // arriendo, servicios, tinta/mantenimiento, etc.
        $table->integer('amount');
        $table->string('note')->nullable();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->timestamps();
        $table->index(['date', 'category']);
        });
    }


    public function down(): void
    {
    Schema::dropIfExists('expenses');
    }
};