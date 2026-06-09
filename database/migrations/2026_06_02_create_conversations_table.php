<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('user_id')->nullable(); // For renters/business seekers
            $table->unsignedBigInteger('apartment_id')->nullable();
            $table->unsignedBigInteger('business_space_id')->nullable();
            $table->string('subject');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->foreign('apartment_id')->references('id')->on('apartments')->onDelete('cascade');
            $table->foreign('business_space_id')->references('id')->on('business_spaces')->onDelete('cascade');
            
            $table->index(['owner_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};