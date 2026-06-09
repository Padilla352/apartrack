<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            // conversation reference
            $table->foreignId('conversation_id')
                ->constrained()
                ->onDelete('cascade');

            // polymorphic sender: tenant (users) or owner (owners)
            $table->morphs('sender');

            // message content
            $table->text('message');

            // read status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // index for faster chat loading
            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};