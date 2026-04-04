<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
};
