<?php

namespace App\Livewire\Digests;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ScheduledDigests extends Component
{
    public function render()
    {
        $workspace = Auth::user()->currentWorkspace();
        return view('livewire.digests.scheduled-digests', [
            'digests' => $workspace ? $workspace->digests()->where('status', 'scheduled')->latest()->get() : collect(),
        ])->layout('layouts.app', ['title' => 'Scheduled']);
    }
}
