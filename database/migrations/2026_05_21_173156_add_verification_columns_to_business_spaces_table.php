<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_spaces', function (Blueprint $table) {
            if (!Schema::hasColumn('business_spaces', 'owner_id')) {
                $table->foreignId('owner_id')->nullable()->after('id')->constrained('owners')->onDelete('cascade');
            }
            if (!Schema::hasColumn('business_spaces', 'images')) {
                $table->json('images')->nullable()->after('image');
            }
            if (!Schema::hasColumn('business_spaces', 'permit_number')) {
                $table->string('permit_number')->nullable()->after('business_features');
            }
            if (!Schema::hasColumn('business_spaces', 'verification_status')) {
                $table->enum('verification_status', ['pending', 'approved', 'rejected', 'permit_invalid', 'permit_mismatch'])->default('pending')->after('status');
            }
            if (!Schema::hasColumn('business_spaces', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('verification_status');
            }
            if (!Schema::hasColumn('business_spaces', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('business_spaces', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_spaces', function (Blueprint $table) {
            $table->dropColumn([
                'owner_id', 'images', 'permit_number',
                'verification_status', 'rejection_reason',
                'verified_at', 'verified_by'
            ]);
        });
    }
};