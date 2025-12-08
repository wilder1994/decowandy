<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document', 50)->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->timestamp('last_purchase_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['archived_at', 'last_purchase_at']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('user_id')
                ->constrained('customers')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });

        Schema::dropIfExists('customers');
    }
};
