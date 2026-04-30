<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('transport_jobs', 'oil_difference_amount')) {
            Schema::table('transport_jobs', function (Blueprint $table) {
                $table->decimal('oil_difference_amount', 12, 2)
                    ->default(0)
                    ->after('oil_difference_liters');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('transport_jobs', 'oil_difference_amount')) {
            Schema::table('transport_jobs', function (Blueprint $table) {
                $table->dropColumn('oil_difference_amount');
            });
        }
    }
};
