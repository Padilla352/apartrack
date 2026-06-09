<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToBusinessSpaces extends Migration
{
    public function up()
    {
        Schema::table('business_spaces', function (Blueprint $table) {
            if (!Schema::hasColumn('business_spaces', 'barangay_name')) {
                $table->string('barangay_name')->nullable()->after('barangay_id');
            }
            if (!Schema::hasColumn('business_spaces', 'permit_number')) {
                $table->string('permit_number')->nullable()->after('barangay_name');
            }
            if (!Schema::hasColumn('business_spaces', 'verification_status')) {
                $table->string('verification_status')->default('pending')->after('status');
            }
            if (!Schema::hasColumn('business_spaces', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verification_status');
            }
            if (!Schema::hasColumn('business_spaces', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            }
            if (!Schema::hasColumn('business_spaces', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('verified_by');
            }
            if (!Schema::hasColumn('business_spaces', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('business_spaces', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    public function down()
    {
        Schema::table('business_spaces', function (Blueprint $table) {
            $table->dropColumn([
                'barangay_name', 'permit_number', 'verification_status',
                'verified_at', 'verified_by', 'rejection_reason',
                'latitude', 'longitude'
            ]);
        });
    }
}