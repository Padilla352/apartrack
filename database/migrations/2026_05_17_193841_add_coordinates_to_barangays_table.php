<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barangays', function (Blueprint $table) {
            // Add missing columns only if they do not exist
            if (!Schema::hasColumn('barangays', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('barangays', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
            // Add timestamps if not present
            if (!Schema::hasColumn('barangays', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        Schema::table('barangays', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
            // Optionally drop timestamps, but careful if other columns depend on them
        });
    }
};