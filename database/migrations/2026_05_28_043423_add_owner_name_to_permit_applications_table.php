<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

     public function up()
{
    Schema::table('permit_applications', function (Blueprint $table) {
        $table->string('owner_name')->nullable()->after('applicant_name');
    });
}
    public function down(): void
    {
        Schema::table('permit_applications', function (Blueprint $table) {
            //
        });
    }
};
