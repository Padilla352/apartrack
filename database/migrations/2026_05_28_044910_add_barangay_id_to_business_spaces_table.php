<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    if (!Schema::hasColumn('business_spaces', 'barangay_name')) {
        Schema::table('business_spaces', function (Blueprint $table) {
            $table->string('barangay_name')->nullable()->after('owner_id');
        });
    }
}
public function down()
{
    Schema::table('business_spaces', function (Blueprint $table) {
        $table->dropForeign(['barangay_id']);
        $table->dropColumn('barangay_id');
    });
}
};
