<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('brand');
            $table->string('model')->nullable();
            $table->decimal('capacity_kg', 10, 2)->nullable();
            $table->decimal('standard_fuel_rate_km_per_liter', 8, 2)->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
