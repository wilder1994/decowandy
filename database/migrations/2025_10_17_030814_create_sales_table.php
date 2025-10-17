<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Ventas: con monto pagado y vuelto (cambio) incluidos */
    public function up(): void {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_code')->unique();          // DW-2025-0001 (lo generaremos al guardar)
            $table->date('date');                           // fecha explícita
            $table->time('time');                           // hora explícita (además de created_at)

            $table->foreignId('user_id')->constrained()->cascadeOnUpdate(); // quién vendió

            // Datos del cliente (opcionales)
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 30)->nullable();

            // Totales de la venta
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('taxes', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Pago y vuelto (lo pediste explícito en el POS)
            $table->decimal('amount_received', 12, 2)->default(0); // monto con el que paga el cliente
            $table->decimal('change_due', 12, 2)->default(0);      // vuelto a entregar (amount_received - total, min 0)

            $table->enum('payment_method', ['cash','transfer','card','mixed','other'])->default('cash');
            $table->text('notes')->nullable();

            // PDF/imagen de factura (a futuro)
            $table->string('invoice_pdf_path')->nullable(); // ruta del PDF
            $table->string('invoice_img_path')->nullable(); // ruta de imagen si la generamos para WhatsApp

            $table->timestamps();
            $table->index(['date','user_id','payment_method']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('sales');
    }
};
