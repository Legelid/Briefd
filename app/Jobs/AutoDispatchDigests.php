<?php

namespace App\Jobs;

use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoDispatchDigests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $workspaces = Workspace::where('schedule_type', '!=', 'manual')
            ->whereNotNull('next_digest_at')
            ->where('next_digest_at', '<=', now())
            ->get();

        foreach ($workspaces as $workspace) {
            $digest = $workspace->digests()->create([
                'title'  => 'Digest — ' . now()->format('M j, Y'),
                'status' => 'generating',
            ]);

            GenerateDigest::withChain([new SendDigest($digest)])
                ->dispatch($digest);

            $workspace->update([
                'last_digest_sent_at' => now(),
                'next_digest_at'      => $workspace->computeNextDigestAt(),
            ]);
        }
    }
}
