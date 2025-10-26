<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
        $table->id();
        $table->date('date');
        $table->string('concept');
        $table->integer('amount');
        $table->string('note')->nullable();
        $table->timestamps();
        $table->index('date');
        });
    }


    public function down(): void
    {
    Schema::dropIfExists('investments');
    }
};