<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
            $table->string('unit_number');
            $table->string('name');
            $table->string('type');
            $table->decimal('monthly_rent', 12, 2);
            $table->enum('status', ['Vacant', 'Occupied', 'Maintenance', 'Reserved'])->default('Vacant');
            $table->string('barangay_name');
            $table->text('address');
            $table->integer('floor_area_sqm')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->string('permit_number')->nullable();
            $table->text('description')->nullable();
            
            // ========== ADD THESE MISSING COLUMNS ==========
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('images')->nullable();
            $table->json('amenities')->nullable();
            // ==============================================
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('verification_status');
            $table->index('owner_id');
            $table->index('barangay_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};