<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermitTypeToPermitApplicationsTable extends Migration
{
    public function up()
    {
        Schema::table('permit_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('permit_applications', 'permit_type')) {
                $table->string('permit_type')->nullable()->after('permit_number');
            }
        });
    }

    public function down()
    {
        Schema::table('permit_applications', function (Blueprint $table) {
            $table->dropColumn('permit_type');
        });
    }
}