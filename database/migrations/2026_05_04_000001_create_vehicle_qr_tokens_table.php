<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_qr_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('access_type', 32);
            $table->string('token', 120)->unique();
            $table->text('pin_code');
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['vehicle_id', 'access_type']);
        });

        Schema::create('vehicle_qr_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_qr_token_id')->constrained('vehicle_qr_tokens')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('access_type', 32);
            $table->string('event_type', 64);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('happened_at');
            $table->timestamps();

            $table->index(['vehicle_id', 'access_type']);
            $table->index(['vehicle_qr_token_id', 'event_type']);
        });

        Schema::table('pre_trip_inspections', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pre_trip_inspections', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });

        Schema::dropIfExists('vehicle_qr_access_logs');
        Schema::dropIfExists('vehicle_qr_tokens');
    }
};
