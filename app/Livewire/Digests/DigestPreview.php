<?php

namespace App\Livewire\Digests;

use App\Mail\DigestMail;
use App\Models\Digest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Locked;
use Livewire\Component;

class DigestPreview extends Component
{
    #[Locked]
    public Digest $digest;

    public bool $sending = false;
    public ?string $sentMessage = null;

    public function mount(Digest $digest): void
    {
        // Ensure it belongs to the current workspace
        $workspace = Auth::user()->currentWorkspace();
        abort_unless($digest->workspace_id === $workspace?->id, 403);
        $this->digest = $digest;
    }

    public function sendDigest(): void
    {
        $this->sending = true;

        $workspace = Auth::user()->currentWorkspace();
        $subscribers = $workspace->subscribers()->whereNull('unsubscribed_at')->get();

        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)->send(new DigestMail($this->digest, $subscriber));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send digest to {$subscriber->email}: " . $e->getMessage());
            }
        }

        $this->digest->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->digest->refresh();
        $this->sending = false;
        $this->sentMessage = "Digest sent to {$subscribers->count()} subscriber(s).";
    }

    public function render()
    {
        return view('livewire.digests.digest-preview')
            ->layout('layouts.app', ['title' => $this->digest->title]);
    }
}
