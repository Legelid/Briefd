<?php

namespace App\Livewire\Digests;

use App\Jobs\SendDigest;
use App\Models\Digest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class DigestPreview extends Component
{
    #[Locked]
    public Digest $digest;

    public ?string $sentMessage = null;

    public function mount(Digest $digest): void
    {
        $workspace = Auth::user()->currentWorkspace();
        abort_unless($digest->workspace_id === $workspace?->id, 403);
        $this->digest = $digest;
    }

    public function sendDigest(): void
    {
        SendDigest::dispatch($this->digest);

        $subscriberCount = Auth::user()->currentWorkspace()
            ->subscribers()->whereNull('unsubscribed_at')->count();

        $this->digest->refresh();
        $this->sentMessage = "Digest queued for {$subscriberCount} subscriber(s).";
    }

    public function render()
    {
        $this->digest->refresh();

        return view('livewire.digests.digest-preview')
            ->layout('layouts.app', ['title' => $this->digest->title]);
    }
}
