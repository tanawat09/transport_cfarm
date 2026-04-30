<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_jobs', function (Blueprint $table) {
            $table->decimal('vehicle_screen_oil_liters', 8, 2)
                ->nullable()
                ->after('actual_oil_liters');
        });
    }

    public function down(): void
    {
        Schema::table('transport_jobs', function (Blueprint $table) {
            $table->dropColumn('vehicle_screen_oil_liters');
        });
    }
};
