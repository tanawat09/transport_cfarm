<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tire_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('tire_serial_number', 100);
            $table->string('tire_position', 10);
            $table->date('installed_at')->nullable();
            $table->decimal('installed_mileage_km', 10, 2)->nullable();
            $table->decimal('removed_mileage_km', 10, 2)->nullable();
            $table->decimal('distance_run_km', 10, 2)->nullable();
            $table->decimal('tread_depth_mm', 5, 2)->nullable();
            $table->string('tire_size', 50)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('condition_status', 20)->default('normal');
            $table->string('vendor_name', 150)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['vehicle_id', 'tire_position']);
            $table->index(['vehicle_id', 'installed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tire_registrations');
    }
};
