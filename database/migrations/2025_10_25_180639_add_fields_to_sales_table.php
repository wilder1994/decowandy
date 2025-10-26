<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'sale_code')) {
                $table->string('sale_code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('sales', 'sold_at')) {
                $table->dateTime('sold_at')->nullable()->after('sale_code');
            }
            if (!Schema::hasColumn('sales', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('sold_at');
            }
            if (!Schema::hasColumn('sales', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('sales', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_email');
            }
            if (!Schema::hasColumn('sales', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('customer_phone');
            }
            if (!Schema::hasColumn('sales', 'payment_method')) {
                $table->string('payment_method', 20)->nullable()->after('user_id'); // cash, transfer, card, mixed, other
            }
            if (!Schema::hasColumn('sales', 'amount_received')) {
                $table->integer('amount_received')->default(0)->after('payment_method');
            }
            if (!Schema::hasColumn('sales', 'total')) {
                $table->integer('total')->default(0)->after('amount_received');
            }
            if (!Schema::hasColumn('sales', 'change_due')) {
                $table->integer('change_due')->default(0)->after('total');
            }
        });
    }


    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            foreach (['sale_code', 'sold_at', 'customer_name', 'customer_email', 'customer_phone', 'user_id', 'payment_method', 'amount_received', 'total', 'change_due'] as $col) {
                if (Schema::hasColumn('sales', $col)) {
                    if ($col === 'user_id') $table->dropConstrainedForeignId('user_id');
                    else $table->dropColumn($col);
                }
            }
        });
    }
};
