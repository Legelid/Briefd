<?php

namespace App\Livewire\Workspaces;

use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WorkspaceList extends Component
{
    public string $name = '';
    public string $description = '';
    public bool $showCreateForm = false;
    public ?int $confirmDeleteId = null;

    // Schedule editing
    public ?int $editScheduleId = null;
    public string $scheduleType = 'manual';
    public string $scheduleDay = 'monday';
    public string $scheduleTime = '09:00';

    public function openCreateForm(): void
    {
        $this->showCreateForm = true;
    }

    public function closeCreateForm(): void
    {
        $this->showCreateForm = false;
        $this->reset('name', 'description');
    }

    public function create(): void
    {
        $user = Auth::user();

        if ($user->plan === 'free' && $user->workspaces()->count() >= 1) {
            $this->addError('name', 'Free plan is limited to 1 workspace. Upgrade to create more.');
            return;
        }

        $this->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string|max:500']);

        $workspace = $user->workspaces()->create([
            'name'        => $this->name,
            'description' => $this->description ?: null,
        ]);

        session(['current_workspace_id' => $workspace->id]);

        $this->reset('name', 'description', 'showCreateForm');
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function delete(): void
    {
        $workspace = Auth::user()->workspaces()->findOrFail($this->confirmDeleteId);
        $workspace->delete();

        if (session('current_workspace_id') === $workspace->id) {
            $first = Auth::user()->workspaces()->first();
            session(['current_workspace_id' => $first?->id]);
        }

        $this->confirmDeleteId = null;
    }

    public function editSchedule(int $id): void
    {
        $workspace            = Auth::user()->workspaces()->findOrFail($id);
        $this->editScheduleId = $id;
        $this->scheduleType   = $workspace->schedule_type ?? 'manual';
        $this->scheduleDay    = $workspace->schedule_day ?? 'monday';
        $this->scheduleTime   = $workspace->schedule_time ?? '09:00';
    }

    public function saveSchedule(): void
    {
        $this->validate([
            'scheduleType' => 'required|in:manual,daily,weekly',
            'scheduleDay'  => 'required_if:scheduleType,weekly|nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'scheduleTime' => 'required_unless:scheduleType,manual|nullable|date_format:H:i',
        ]);

        $workspace = Auth::user()->workspaces()->findOrFail($this->editScheduleId);

        $workspace->schedule_type = $this->scheduleType;
        $workspace->schedule_day  = $this->scheduleType === 'weekly' ? $this->scheduleDay : null;
        $workspace->schedule_time = $this->scheduleType !== 'manual' ? $this->scheduleTime : null;
        $workspace->save();

        $workspace->next_digest_at = $workspace->computeNextDigestAt();
        $workspace->save();

        $this->editScheduleId = null;
    }

    public function cancelSchedule(): void
    {
        $this->editScheduleId = null;
    }

    public function render()
    {
        return view('livewire.workspaces.workspace-list', [
            'workspaces' => Auth::user()->workspaces()->withCount(['sources', 'subscribers', 'digests'])->get(),
        ])->layout('layouts.app', ['title' => 'Workspaces']);
    }
}
