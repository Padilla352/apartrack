<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_spaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('barangay');
            $table->decimal('price', 12, 2);
            $table->string('type')->nullable(); // retail, office, commercial, etc.
            $table->string('image')->nullable(); // main image
            $table->json('images')->nullable(); // gallery images
            $table->json('amenities')->nullable();
            $table->json('business_features')->nullable();
            $table->string('permit_number')->nullable();
            $table->enum('status', ['Vacant', 'Occupied'])->default('Vacant');
            $table->enum('verification_status', ['pending', 'approved', 'rejected', 'permit_invalid', 'permit_mismatch'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_spaces');
    }
};