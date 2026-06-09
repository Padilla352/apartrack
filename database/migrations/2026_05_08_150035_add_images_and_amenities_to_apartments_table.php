<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('apartments', function (Blueprint $table) {
        if (!Schema::hasColumn('apartments', 'images')) {
            $table->json('images')->nullable()->after('status');
        }
        // other columns like 'amenities' etc.
    });
}

    public function down()
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn(['images', 'amenities']);
        });
    }
};