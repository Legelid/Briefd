<?php

namespace App\Livewire\Subscribers;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SubscriberList extends Component
{
    use WithPagination;

    public string $name = '';
    public string $email = '';
    public bool $showCreateForm = false;
    public ?int $confirmDeleteId = null;

    public function openCreateForm(): void
    {
        $this->showCreateForm = true;
    }

    public function closeCreateForm(): void
    {
        $this->showCreateForm = false;
        $this->reset('name', 'email');
    }

    public function create(): void
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace();

        if ($user->plan === 'free' && $workspace->subscribers()->count() >= 50) {
            $this->addError('email', 'Free plan is limited to 50 subscribers. Upgrade to add more.');
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        try {
            $workspace->subscribers()->create([
                'name' => $this->name,
                'email' => $this->email,
            ]);
            $this->reset('name', 'email', 'showCreateForm');
            $this->resetPage();
        } catch (\Illuminate\Database\QueryException $e) {
            $this->addError('email', 'This email is already subscribed.');
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function delete(): void
    {
        $workspace = Auth::user()->currentWorkspace();
        $workspace->subscribers()->findOrFail($this->confirmDeleteId)->delete();
        $this->confirmDeleteId = null;
        $this->resetPage();
    }

    public function render()
    {
        $workspace = Auth::user()->currentWorkspace();
        $count = $workspace ? $workspace->subscribers()->count() : 0;
        return view('livewire.subscribers.subscriber-list', [
            'subscribers' => $workspace ? $workspace->subscribers()->latest()->paginate(20) : collect(),
            'totalCount' => $count,
            'atLimit' => Auth::user()->plan === 'free' && $count >= 50,
        ])->layout('layouts.app', ['title' => 'Subscribers']);
    }
}
