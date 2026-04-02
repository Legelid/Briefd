<?php

namespace App\Livewire;

use App\Models\Digest;
use App\Models\Source;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    public ?int $workspaceId = null;

    public function mount(): void
    {
        $this->workspaceId = session('current_workspace_id');
    }

    #[Computed]
    public function workspace()
    {
        return Auth::user()->currentWorkspace();
    }

    #[Computed]
    public function stats(): array
    {
        if (! $this->workspace) return ['subscribers' => 0, 'digests_sent' => 0, 'active_sources' => 0];
        return [
            'subscribers' => $this->workspace->subscribers()->count(),
            'digests_sent' => $this->workspace->digests()->where('status', 'sent')->count(),
            'active_sources' => $this->workspace->sources()->where('status', 'healthy')->count(),
        ];
    }

    #[Computed]
    public function recentDigests()
    {
        if (! $this->workspace) return collect();
        return $this->workspace->digests()->latest()->limit(3)->get();
    }

    #[Computed]
    public function sources()
    {
        if (! $this->workspace) return collect();
        return $this->workspace->sources()->withCount('items')->get();
    }

    #[Computed]
    public function nextDigest()
    {
        if (! $this->workspace) return null;
        return $this->workspace->digests()->where('status', 'scheduled')->latest()->first();
    }

    #[Computed]
    public function unhealthySources(): int
    {
        if (! $this->workspace) return 0;
        return $this->workspace->sources()->where('status', '!=', 'healthy')->count();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
