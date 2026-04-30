<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->date('usage_date');
            $table->string('usage_month', 7)->index();
            $table->decimal('odometer_start', 12, 2)->nullable();
            $table->decimal('odometer_end', 12, 2)->nullable();
            $table->decimal('distance_km', 12, 2)->default(0);
            $table->string('purpose', 255)->nullable();
            $table->string('destination', 255)->nullable();
            $table->decimal('fuel_liters', 10, 2)->nullable();
            $table->decimal('fuel_price_per_liter', 10, 2)->nullable();
            $table->decimal('fuel_total_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['vehicle_id', 'usage_date']);
            $table->index(['vehicle_id', 'usage_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_usage_logs');
    }
};
