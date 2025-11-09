<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('stocks', 'min_threshold')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->integer('min_threshold')->nullable()->after('quantity');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stocks', 'min_threshold')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropColumn('min_threshold');
            });
        }
    }
};
