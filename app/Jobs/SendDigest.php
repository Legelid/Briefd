<?php

namespace App\Jobs;

use App\Mail\DigestMail;
use App\Models\Digest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Digest $digest) {}

    public function handle(): void
    {
        $digest = $this->digest->fresh();

        if ($digest->status !== 'draft') {
            return;
        }

        $subscribers = $digest->workspace->subscribers()
            ->whereNull('unsubscribed_at')
            ->get();

        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)->send(new DigestMail($digest, $subscriber));
            } catch (\Throwable $e) {
                Log::error("SendDigest: failed to send to {$subscriber->email}: " . $e->getMessage());
            }
        }

        $digest->update(['status' => 'sent', 'sent_at' => now()]);
    }
}
