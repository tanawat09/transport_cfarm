<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_jobs', function (Blueprint $table) {
            $table->string('calculation_status', 20)->default('pending')->after('average_fuel_rate_km_per_liter');
            $table->text('calculation_note')->nullable()->after('calculation_status');
            $table->timestamp('calculated_at')->nullable()->after('calculation_note');
        });
    }

    public function down(): void
    {
        Schema::table('transport_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'calculation_status',
                'calculation_note',
                'calculated_at',
            ]);
        });
    }
};
