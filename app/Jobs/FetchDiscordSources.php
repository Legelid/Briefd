<?php

namespace App\Jobs;

use App\Models\Source;
use App\Models\SourceItem;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchDiscordSources implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $sources = Source::where('type', 'discord')->get();

        foreach ($sources as $source) {
            try {
                foreach ($source->discord_channel_ids ?? [] as $channelId) {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bot ' . config('services.discord.bot_token'),
                    ])->get("https://discord.com/api/v10/channels/{$channelId}/messages", ['limit' => 20]);

                    if (! $response->successful()) {
                        continue;
                    }

                    foreach ($response->json() as $message) {
                        if (empty($message['content'])) continue;

                        $url = "https://discord.com/channels/{$source->discord_guild_id}/{$channelId}/{$message['id']}";

                        SourceItem::updateOrCreate(
                            ['source_id' => $source->id, 'url' => $url],
                            [
                                'title'        => mb_substr($message['content'], 0, 150),
                                'summary'      => $message['content'],
                                'published_at' => Carbon::parse($message['timestamp']),
                            ]
                        );
                    }
                }

                $source->update(['status' => 'healthy', 'last_fetched_at' => now()]);
            } catch (\Throwable $e) {
                Log::warning("FetchDiscordSources failed for source #{$source->id}: " . $e->getMessage());
                $source->update(['status' => 'warning']);
            }
        }
    }
}
