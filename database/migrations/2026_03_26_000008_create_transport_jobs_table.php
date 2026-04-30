<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_jobs', function (Blueprint $table) {
            $table->id();
            $table->date('transport_date')->index();
            $table->string('document_no')->unique();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignId('driver_id')->constrained()->restrictOnDelete();
            $table->foreignId('farm_id')->constrained()->restrictOnDelete();
            $table->foreignId('vendor_id')->constrained()->restrictOnDelete();
            $table->foreignId('route_standard_id')->constrained()->restrictOnDelete();
            $table->decimal('food_weight_kg', 10, 2)->default(0);
            $table->decimal('odometer_start', 12, 2);
            $table->decimal('odometer_end', 12, 2);
            $table->decimal('actual_distance_km', 10, 2);
            $table->decimal('standard_distance_km', 10, 2);
            $table->decimal('company_oil_liters', 8, 2);
            $table->decimal('oil_compensation_liters', 8, 2)->default(0);
            $table->foreignId('oil_compensation_reason_id')->nullable()->constrained()->nullOnDelete();
            $table->text('oil_compensation_details')->nullable();
            $table->decimal('approved_oil_liters', 8, 2);
            $table->decimal('actual_oil_liters', 8, 2)->default(0);
            $table->decimal('oil_price_per_liter', 8, 2)->default(0);
            $table->decimal('total_oil_cost', 12, 2)->default(0);
            $table->decimal('oil_difference_liters', 8, 2)->default(0);
            $table->decimal('oil_difference_amount', 12, 2)->default(0);
            $table->decimal('distance_difference_km', 10, 2)->default(0);
            $table->decimal('average_fuel_rate_km_per_liter', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['vehicle_id', 'transport_date']);
            $table->index(['driver_id', 'transport_date']);
            $table->index(['farm_id', 'transport_date']);
            $table->index(['vendor_id', 'transport_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_jobs');
    }
};
