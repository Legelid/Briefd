<?php

namespace App\Livewire\Digests;

use App\Jobs\GenerateDigest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DigestList extends Component
{
    public ?int $confirmDeleteId = null;

    public function generateDigest(): void
    {
        $workspace = Auth::user()->currentWorkspace();

        $digest = $workspace->digests()->create([
            'title' => 'Digest — ' . now()->format('M j, Y'),
            'status' => 'generating',
        ]);

        GenerateDigest::dispatch($digest);

        $this->redirect(route('digests.preview', $digest), navigate: true);
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function delete(): void
    {
        $workspace = Auth::user()->currentWorkspace();
        $workspace->digests()->findOrFail($this->confirmDeleteId)->delete();
        $this->confirmDeleteId = null;
    }

    public function render()
    {
        $workspace = Auth::user()->currentWorkspace();
        $digests = $workspace ? $workspace->digests()->latest()->get() : collect();

        return view('livewire.digests.digest-list', [
            'digests' => $digests,
            'hasGenerating' => $digests->contains('status', 'generating'),
        ])->layout('layouts.app', ['title' => 'Digests']);
    }
}
