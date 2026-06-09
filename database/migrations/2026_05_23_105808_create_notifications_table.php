<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('type')->nullable(); // registration, approval, rejection, etc.
                $table->string('title');
                $table->text('message');
                $table->json('data')->nullable();
                $table->string('target_role')->default('admin'); // admin, owner, tenant
                $table->unsignedBigInteger('target_id')->nullable(); // specific user ID
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                
                $table->index(['target_role', 'is_read']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};