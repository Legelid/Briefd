<?php

namespace App\Jobs;

use App\Models\Digest;
use App\Services\ClaudeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public function __construct(public readonly Digest $digest) {}

    public function handle(ClaudeService $claude): void
    {
        $workspace = $this->digest->workspace;

        $sources = $workspace->sources()
            ->with(['items' => fn ($q) => $q->latest('published_at')->limit(5)])
            ->get();

        $groupedSources = $sources
            ->filter(fn ($source) => $source->items->isNotEmpty())
            ->map(fn ($source) => ['source' => $source, 'items' => $source->items])
            ->values();

        try {
            $content = $claude->generateDigest($groupedSources);
            $this->digest->update(['status' => 'draft', 'content' => $content]);
        } catch (\Throwable $e) {
            $this->digest->update([
                'status'  => 'draft',
                'content' => '<p>Failed to generate digest. Please try again.</p>',
            ]);
        }
    }
}
