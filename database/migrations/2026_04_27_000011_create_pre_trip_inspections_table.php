<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_trip_inspections', function (Blueprint $table) {
            $table->id();
            $table->date('inspection_date')->index();
            $table->time('inspection_time');
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->decimal('odometer_km', 12, 2)->nullable();
            $table->string('engine_oil_and_fluids_status', 20);
            $table->text('engine_oil_and_fluids_note')->nullable();
            $table->string('belt_status', 20);
            $table->text('belt_note')->nullable();
            $table->string('lights_status', 20);
            $table->text('lights_note')->nullable();
            $table->string('leak_status', 20);
            $table->text('leak_note')->nullable();
            $table->string('parking_brake_status', 20);
            $table->text('parking_brake_note')->nullable();
            $table->string('pedals_and_steering_status', 20);
            $table->text('pedals_and_steering_note')->nullable();
            $table->string('tires_and_wheels_status', 20);
            $table->text('tires_and_wheels_note')->nullable();
            $table->boolean('is_ready_to_drive')->default(true)->index();
            $table->text('overall_note')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'inspection_date']);
            $table->index(['driver_id', 'inspection_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_trip_inspections');
    }
};
