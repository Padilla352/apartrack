<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPropertyTypeToPermitApplicationsTable extends Migration
{
    public function up()
    {
        Schema::table('permit_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('permit_applications', 'property_type')) {
                $table->string('property_type')->nullable()->after('business_name');
            }
        });
    }

    public function down()
    {
        Schema::table('permit_applications', function (Blueprint $table) {
            $table->dropColumn('property_type');
        });
    }
}