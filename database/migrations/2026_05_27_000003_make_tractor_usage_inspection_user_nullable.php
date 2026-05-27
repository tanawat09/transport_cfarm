<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tractor_usage_inspections', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        $fallbackUserId = User::query()->value('id');

        if ($fallbackUserId !== null) {
            DB::table('tractor_usage_inspections')
                ->whereNull('user_id')
                ->update(['user_id' => $fallbackUserId]);
        }

        Schema::table('tractor_usage_inspections', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }
};
