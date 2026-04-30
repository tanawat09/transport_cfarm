<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('document_type', 30);
            $table->string('document_no', 100)->nullable();
            $table->string('provider_name', 150)->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at');
            $table->unsignedSmallInteger('alert_before_days')->default(30);
            $table->boolean('is_alert_enabled')->default(true);
            $table->text('notes')->nullable();
            $table->timestamp('last_telegram_notified_at')->nullable();
            $table->timestamps();

            $table->unique(['vehicle_id', 'document_type']);
            $table->index(['expires_at', 'is_alert_enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
