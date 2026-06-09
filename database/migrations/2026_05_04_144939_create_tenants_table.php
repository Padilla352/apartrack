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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->nullable()->constrained()->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('barangay_id')->nullable()->constrained()->onDelete('set null');
            $table->date('move_in_date');
            $table->date('lease_end_date')->nullable();
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->decimal('monthly_rent', 12, 2)->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Pending', 'Evicted'])->default('Active');
            $table->json('emergency_contact')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'move_in_date']);
            $table->index('lease_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
