<?php

namespace App\Jobs;

use App\Models\Source;
use App\Models\SourceItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchRssSources implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        foreach (Source::where('type', 'rss')->get() as $source) {
            try {
                $body = Http::timeout(15)->get($source->url)->body();
                $feed = @simplexml_load_string($body);

                if ($feed === false) {
                    throw new \RuntimeException('Invalid XML from feed: ' . $source->url);
                }

                $channel = $feed->channel ?? $feed;
                $items = $channel->item ?? [];

                foreach ($items as $item) {
                    $url = (string) ($item->link ?? $item->guid ?? '');
                    $title = (string) ($item->title ?? 'Untitled');

                    if (! $url) continue;

                    $pubDate = null;
                    if (! empty($item->pubDate)) {
                        try {
                            $pubDate = \Carbon\Carbon::parse((string) $item->pubDate);
                        } catch (\Throwable) {}
                    }

                    SourceItem::updateOrCreate(
                        ['source_id' => $source->id, 'url' => $url],
                        ['title' => $title, 'published_at' => $pubDate]
                    );
                }

                $source->update(['status' => 'healthy', 'last_fetched_at' => now()]);
            } catch (\Throwable $e) {
                Log::warning("FetchRssSources failed for source #{$source->id}: " . $e->getMessage());
                $source->update(['status' => 'warning']);
            }
        }
    }
}
