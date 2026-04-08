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
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('schedule_type')->default('manual')->after('description');
            $table->string('schedule_day')->nullable()->after('schedule_type');
            $table->time('schedule_time')->nullable()->after('schedule_day');
            $table->timestamp('last_digest_sent_at')->nullable()->after('schedule_time');
            $table->timestamp('next_digest_at')->nullable()->after('last_digest_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn(['schedule_type', 'schedule_day', 'schedule_time', 'last_digest_sent_at', 'next_digest_at']);
        });
    }
};
