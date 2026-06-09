<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSenderTypeToMessagesTableIfMissing extends Migration
{
    public function up()
    {
        // Only add the column if it doesn't already exist
        if (!Schema::hasColumn('messages', 'sender_type')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->enum('sender_type', ['tenant', 'owner'])->after('sender_id')->nullable(false);
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('messages', 'sender_type')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropColumn('sender_type');
            });
        }
    }
}