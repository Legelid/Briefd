<?php

namespace App\Livewire\Digests;

use App\Models\Digest;
use App\Services\ClaudeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DigestList extends Component
{
    public bool $generating = false;
    public ?int $confirmDeleteId = null;

    public function generateDigest(): void
    {
        $this->generating = true;

        $workspace = Auth::user()->currentWorkspace();

        $digest = $workspace->digests()->create([
            'title' => 'Digest — ' . now()->format('M j, Y'),
            'status' => 'draft',
        ]);

        $items = \App\Models\SourceItem::whereIn(
            'source_id',
            $workspace->sources()->pluck('id')
        )->latest('published_at')->limit(20)->get();

        try {
            $content = app(ClaudeService::class)->generateDigest($items);
            $digest->update(['content' => $content]);
        } catch (\Throwable $e) {
            $digest->update(['content' => '<p>Failed to generate digest. Please try again.</p>']);
        }

        $this->generating = false;

        $this->redirect(route('digests.preview', $digest), navigate: true);
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
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
        return view('livewire.digests.digest-list', [
            'digests' => $workspace ? $workspace->digests()->latest()->get() : collect(),
        ])->layout('layouts.app', ['title' => 'Digests']);
    }
}
