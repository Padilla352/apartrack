<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'maintenance_notifications')) {
                $table->boolean('maintenance_notifications')->default(true)->after('address');
            }
            if (!Schema::hasColumn('users', 'announcement_notifications')) {
                $table->boolean('announcement_notifications')->default(true)->after('maintenance_notifications');
            }
            if (!Schema::hasColumn('users', 'email_notifications')) {
                $table->boolean('email_notifications')->default(true)->after('announcement_notifications');
            }
            if (!Schema::hasColumn('users', 'push_notifications')) {
                $table->boolean('push_notifications')->default(true)->after('email_notifications');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'maintenance_notifications',
                'announcement_notifications',
                'email_notifications',
                'push_notifications'
            ]);
        });
    }
};