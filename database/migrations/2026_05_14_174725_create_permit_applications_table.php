<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permit_applications', function (Blueprint $table) {
            $table->id();
            $table->string('permit_number')->unique();
            $table->string('business_name');
            $table->string('owner_name');
            $table->string('property_name')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->text('address')->nullable();
            $table->string('barangay')->nullable();
            $table->string('business_type')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->date('application_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('documents')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('permit_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permit_applications');
    }
};