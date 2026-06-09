<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('permit_numbers', function (Blueprint $table) {
            if (!Schema::hasColumn('permit_numbers', 'permit_type')) {
                $table->enum('permit_type', ['residential', 'business'])->nullable()->after('permit_number');
            }
        });
    }

    public function down()
    {
        Schema::table('permit_numbers', function (Blueprint $table) {
            $table->dropColumn('permit_type');
        });
    }
};