<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tractor_usage_inspections', function (Blueprint $table) {
            $table->foreignId('farm_id')
                ->nullable()
                ->after('driver_id')
                ->constrained()
                ->nullOnDelete();

            $table->index(['farm_id', 'inspection_date']);
        });
    }

    public function down(): void
    {
        Schema::table('tractor_usage_inspections', function (Blueprint $table) {
            $table->dropIndex(['farm_id', 'inspection_date']);
            $table->dropConstrainedForeignId('farm_id');
        });
    }
};
