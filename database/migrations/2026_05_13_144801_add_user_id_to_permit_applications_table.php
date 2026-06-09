<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if the table exists
        if (!Schema::hasTable('permit_applications')) {
            // Create the table if it doesn't exist
            Schema::create('permit_applications', function (Blueprint $table) {
                $table->id();
                $table->string('permit_number')->unique();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('full_name')->nullable();
                $table->string('email')->nullable();
                $table->string('contact_number')->nullable();
                $table->string('business_name')->nullable();
                $table->string('business_address')->nullable();
                $table->string('barangay')->nullable();
                $table->text('additional_details')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();
            });
        } else {
            // If table exists, just add the column if missing
            Schema::table('permit_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('permit_applications', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('permit_number');
                }
            });
        }
    }

    public function down(): void
    {
        // Drop the column if table exists, but do NOT drop the entire table
        if (Schema::hasTable('permit_applications') && Schema::hasColumn('permit_applications', 'user_id')) {
            Schema::table('permit_applications', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
};