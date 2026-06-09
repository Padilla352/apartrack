<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            if (!Schema::hasColumn('owners', 'property_type')) {
                $table->enum('property_type', ['apartment', 'business', 'both'])->default('apartment')->after('permit_number');
            }
            if (!Schema::hasColumn('owners', 'residential_permit')) {
                $table->string('residential_permit')->nullable()->after('property_type');
            }
            if (!Schema::hasColumn('owners', 'business_permit')) {
                $table->string('business_permit')->nullable()->after('residential_permit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn(['property_type', 'residential_permit', 'business_permit']);
        });
    }
};