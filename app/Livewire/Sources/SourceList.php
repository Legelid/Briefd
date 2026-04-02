<?php

namespace App\Livewire\Sources;

use App\Models\Source;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SourceList extends Component
{
    public string $name = '';
    public string $url = '';
    public bool $showCreateForm = false;
    public ?int $confirmDeleteId = null;

    public function openCreateForm(): void
    {
        $this->showCreateForm = true;
    }

    public function closeCreateForm(): void
    {
        $this->showCreateForm = false;
        $this->reset('name', 'url');
    }

    public function create(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
        ]);

        $workspace = Auth::user()->currentWorkspace();

        $workspace->sources()->create([
            'name' => $this->name,
            'url' => $this->url,
            'type' => 'rss',
            'status' => 'healthy',
        ]);

        $this->reset('name', 'url', 'showCreateForm');
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function delete(): void
    {
        $workspace = Auth::user()->currentWorkspace();
        $source = $workspace->sources()->findOrFail($this->confirmDeleteId);
        $source->delete();
        $this->confirmDeleteId = null;
    }

    public function render()
    {
        $workspace = Auth::user()->currentWorkspace();
        return view('livewire.sources.source-list', [
            'sources' => $workspace ? $workspace->sources()->withCount('items')->latest()->get() : collect(),
        ])->layout('layouts.app', ['title' => 'Sources']);
    }
}
