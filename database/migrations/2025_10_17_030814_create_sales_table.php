<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Ventas con código, fecha/hora explícita y montos en enteros */
    public function up(): void {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_code')->unique();
            $table->dateTime('sold_at')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();

            // Datos del cliente (opcionales)
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 30)->nullable();

            // Totales de la venta (enteros COP)
            $table->integer('subtotal')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('taxes')->default(0);
            $table->integer('total')->default(0);

            // Pago y vuelto
            $table->integer('amount_received')->default(0);
            $table->integer('change_due')->default(0);

            $table->enum('payment_method', ['cash','transfer','card','mixed','other'])->default('cash');
            $table->text('notes')->nullable();

            // PDF/imagen de factura (a futuro)
            $table->string('invoice_pdf_path')->nullable();
            $table->string('invoice_img_path')->nullable();

            $table->timestamps();
            $table->index(['date','user_id','payment_method']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('sales');
    }
};
