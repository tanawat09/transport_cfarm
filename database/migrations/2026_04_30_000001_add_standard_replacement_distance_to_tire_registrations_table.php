<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tire_registrations', function (Blueprint $table) {
            $table->decimal('standard_replacement_distance_km', 10, 2)
                ->nullable()
                ->after('installed_mileage_km');
        });
    }

    public function down(): void
    {
        Schema::table('tire_registrations', function (Blueprint $table) {
            $table->dropColumn('standard_replacement_distance_km');
        });
    }
};
