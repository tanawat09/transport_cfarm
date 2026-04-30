<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_documents', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicle_documents', 'insurance_capital')) {
                $table->decimal('insurance_capital', 12, 2)->nullable()->after('provider_name');
            }

            if (! Schema::hasColumn('vehicle_documents', 'tax_expires_at')) {
                $table->date('tax_expires_at')->nullable()->after('expires_at');
            }

            if (! Schema::hasColumn('vehicle_documents', 'compulsory_expires_at')) {
                $table->date('compulsory_expires_at')->nullable()->after('tax_expires_at');
            }

            if (! Schema::hasColumn('vehicle_documents', 'insurance_expires_at')) {
                $table->date('insurance_expires_at')->nullable()->after('compulsory_expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_documents', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('vehicle_documents', 'insurance_capital') ? 'insurance_capital' : null,
                Schema::hasColumn('vehicle_documents', 'tax_expires_at') ? 'tax_expires_at' : null,
                Schema::hasColumn('vehicle_documents', 'compulsory_expires_at') ? 'compulsory_expires_at' : null,
                Schema::hasColumn('vehicle_documents', 'insurance_expires_at') ? 'insurance_expires_at' : null,
            ]));

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
