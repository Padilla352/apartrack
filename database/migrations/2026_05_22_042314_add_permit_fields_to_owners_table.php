<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('owners', function (Blueprint $table) {
            if (!Schema::hasColumn('owners', 'property_type')) {
                $table->string('property_type')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('owners', 'residential_permit')) {
                $table->string('residential_permit')->nullable()->after('property_type');
            }
            if (!Schema::hasColumn('owners', 'business_permit')) {
                $table->string('business_permit')->nullable()->after('residential_permit');
            }
            if (!Schema::hasColumn('owners', 'permit_numbers')) {
                $table->text('permit_numbers')->nullable()->after('business_permit');
            }
        });
    }

    public function down()
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn(['property_type', 'residential_permit', 'business_permit', 'permit_numbers']);
        });
    }
};