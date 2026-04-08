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
        Schema::table('sources', function (Blueprint $table) {
            $table->string('discord_guild_id')->nullable()->after('url');
            $table->json('discord_channel_ids')->nullable()->after('discord_guild_id');
            $table->text('discord_access_token')->nullable()->after('discord_channel_ids');
        });
    }

    public function down(): void
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->dropColumn(['discord_guild_id', 'discord_channel_ids', 'discord_access_token']);
        });
    }
};
