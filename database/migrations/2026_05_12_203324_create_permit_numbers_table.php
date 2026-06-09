<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permit_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('permit_number')->unique();
            $table->enum('permit_type', ['residential', 'business'])->nullable(); // ← ADD THIS
            $table->string('owner_name');
            $table->string('property_name')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('used_at')->nullable();
            $table->unsignedBigInteger('used_by')->nullable(); // ← ADD THIS
            $table->timestamps();
            
            // Add foreign key constraint
            $table->foreign('used_by')->references('id')->on('owners')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permit_numbers');
    }
};