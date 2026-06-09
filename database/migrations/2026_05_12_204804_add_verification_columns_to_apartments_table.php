<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            // Only add verification_status if it doesn't exist
            if (!Schema::hasColumn('apartments', 'verification_status')) {
                $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            }
            
            // Only add rejection_reason if it doesn't exist
            if (!Schema::hasColumn('apartments', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('verification_status');
            }
            
            // Only add verified_at if it doesn't exist
            if (!Schema::hasColumn('apartments', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('rejection_reason');
            }
            
            // Only add verified_by if it doesn't exist
            if (!Schema::hasColumn('apartments', 'verified_by')) {
                $table->bigInteger('verified_by')->nullable()->after('verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn([
                'verification_status',
                'rejection_reason',
                'verified_at',
                'verified_by'
            ]);
        });
    }
};