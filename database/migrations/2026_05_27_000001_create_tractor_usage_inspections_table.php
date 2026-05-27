<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tractor_usage_inspections', function (Blueprint $table) {
            $table->id();
            $table->date('inspection_date')->index();
            $table->time('inspection_time');
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
            $table->decimal('working_hours', 10, 2);
            $table->json('checklist_results');
            $table->boolean('is_ready_for_use')->default(true)->index();
            $table->text('overall_note')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'inspection_date']);
            $table->index(['driver_id', 'inspection_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tractor_usage_inspections');
    }
};
