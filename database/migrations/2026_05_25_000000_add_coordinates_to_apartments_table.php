<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            // Only add latitude if it doesn't exist
            if (!Schema::hasColumn('apartments', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('rejection_reason');
            }
            
            // Only add longitude if it doesn't exist
            if (!Schema::hasColumn('apartments', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
