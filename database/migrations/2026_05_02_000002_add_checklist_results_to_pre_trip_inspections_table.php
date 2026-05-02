<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_trip_inspections', function (Blueprint $table) {
            $table->json('checklist_results')->nullable()->after('tires_and_wheels_note');

            $table->string('engine_oil_and_fluids_status', 20)->nullable()->change();
            $table->string('belt_status', 20)->nullable()->change();
            $table->string('lights_status', 20)->nullable()->change();
            $table->string('leak_status', 20)->nullable()->change();
            $table->string('parking_brake_status', 20)->nullable()->change();
            $table->string('pedals_and_steering_status', 20)->nullable()->change();
            $table->string('tires_and_wheels_status', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pre_trip_inspections', function (Blueprint $table) {
            $table->dropColumn('checklist_results');

            $table->string('engine_oil_and_fluids_status', 20)->nullable(false)->change();
            $table->string('belt_status', 20)->nullable(false)->change();
            $table->string('lights_status', 20)->nullable(false)->change();
            $table->string('leak_status', 20)->nullable(false)->change();
            $table->string('parking_brake_status', 20)->nullable(false)->change();
            $table->string('pedals_and_steering_status', 20)->nullable(false)->change();
            $table->string('tires_and_wheels_status', 20)->nullable(false)->change();
        });
    }
};
