<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacebookUsernameToOwnersTable extends Migration
{
    public function up()
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->string('facebook_username')->nullable()->after('phone');
        });
    }

    public function down()
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn('facebook_username');
        });
    }
}