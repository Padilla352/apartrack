<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSenderTypeToMessagesTable extends Migration
{
    public function up()
    {
        // Check if column already exists to avoid "Duplicate column" error
        if (!Schema::hasColumn('messages', 'sender_type')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->enum('sender_type', ['tenant', 'owner'])->after('sender_id')->nullable(false);
            });
        }
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('sender_type');
        });
    }
}