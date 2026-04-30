<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_jobs', function (Blueprint $table) {
            $table->string('other_job_description')->nullable()->after('odometer_end');
            $table->decimal('other_job_odometer_start', 10, 2)->nullable()->after('other_job_description');
            $table->decimal('other_job_odometer_end', 10, 2)->nullable()->after('other_job_odometer_start');
            $table->decimal('other_job_distance_km', 10, 2)->default(0)->after('other_job_odometer_end');
        });
    }

    public function down(): void
    {
        Schema::table('transport_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'other_job_description',
                'other_job_odometer_start',
                'other_job_odometer_end',
                'other_job_distance_km',
            ]);
        });
    }
};
