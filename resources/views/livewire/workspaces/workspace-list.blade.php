<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-white">Workspaces</h2>
            <p class="text-sm mt-1" style="color: #6b6b8a;">Manage your workspaces</p>
        </div>
        @if(auth()->user()->plan !== 'free' || $workspaces->count() < 1)
            <button wire:click="openCreateForm"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-white"
                    style="background-color: #ff6b2b;">
                + New Workspace
            </button>
        @endif
    </div>

    @if(auth()->user()->plan === 'free' && $workspaces->count() >= 1)
        <div class="mb-6 p-4 rounded-xl border" style="background-color: #1f1008; border-color: #ff6b2b;">
            <p class="text-sm font-medium" style="color: #ff6b2b;">Free plan: 1 workspace max</p>
            <p class="text-sm mt-1" style="color: #6b6b8a;">Upgrade to Creator or Pro to create multiple workspaces.</p>
            <button class="mt-3 px-3 py-1.5 rounded-lg text-xs font-medium text-white opacity-50 cursor-not-allowed" style="background-color: #ff6b2b;" disabled>
                Upgrade Plan
            </button>
        </div>
    @endif

    @if($showCreateForm)
        <div class="mb-6 p-5 rounded-xl border" style="background-color: #0f0f18; border-color: #1e1e2e;">
            <h3 class="text-sm font-semibold text-white mb-4">Create Workspace</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Name</label>
                    <input wire:model="name" type="text" placeholder="My Workspace"
                           class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none"
                           style="background-color: #0a0a0f; border-color: #1e1e2e;">
                    @error('name') <p class="text-xs mt-1" style="color: #e24b4a;">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Description (optional)</label>
                    <input wire:model="description" type="text"
                           class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none"
                           style="background-color: #0a0a0f; border-color: #1e1e2e;">
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button wire:click="create" class="px-4 py-2 rounded-lg text-sm font-medium text-white" style="background-color: #ff6b2b;">Create</button>
                <button wire:click="closeCreateForm" class="px-4 py-2 rounded-lg text-sm font-medium border" style="border-color: #1e1e2e; color: #6b6b8a;">Cancel</button>
            </div>
        </div>
    @endif

    <div class="space-y-3">
        @forelse($workspaces as $workspace)
            <div class="p-5 rounded-xl border" style="background-color: #0f0f18; border-color: #1e1e2e;">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-semibold text-white">{{ $workspace->name }}</h3>
                            @if(session('current_workspace_id') == $workspace->id)
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background-color: #1f1008; color: #ff6b2b;">Active</span>
                            @endif
                        </div>
                        @if($workspace->description)
                            <p class="text-xs mt-1" style="color: #6b6b8a;">{{ $workspace->description }}</p>
                        @endif
                        <div class="flex flex-wrap gap-4 mt-2">
                            <span class="text-xs" style="color: #6b6b8a;">{{ $workspace->sources_count }} sources</span>
                            <span class="text-xs" style="color: #6b6b8a;">{{ $workspace->subscribers_count }} subscribers</span>
                            <span class="text-xs" style="color: #6b6b8a;">{{ $workspace->digests_count }} digests</span>
                            <span class="text-xs" style="color: #6b6b8a;">
                                Schedule:
                                @if($workspace->schedule_type === 'daily')
                                    Daily at {{ $workspace->schedule_time }}
                                @elseif($workspace->schedule_type === 'weekly')
                                    Weekly on {{ ucfirst($workspace->schedule_day) }} at {{ $workspace->schedule_time }}
                                @else
                                    Manual
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-4 flex-shrink-0">
                        <button wire:click="editSchedule({{ $workspace->id }})"
                                class="text-xs px-2 py-1 rounded border"
                                style="border-color: #1e1e2e; color: #6b6b8a;">
                            Schedule
                        </button>
                        @if($workspaces->count() > 1)
                            @if($confirmDeleteId === $workspace->id)
                                <div class="flex items-center gap-2">
                                    <span class="text-xs" style="color: #e24b4a;">Delete?</span>
                                    <button wire:click="delete" class="text-xs px-2 py-1 rounded" style="background-color: #e24b4a; color: white;">Yes</button>
                                    <button wire:click="$set('confirmDeleteId',null)" class="text-xs px-2 py-1 rounded border" style="border-color: #1e1e2e; color: #6b6b8a;">No</button>
                                </div>
                            @else
                                <button wire:click="confirmDelete({{ $workspace->id }})" class="text-xs transition-colors" style="color: #6b6b8a;">Delete</button>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Schedule edit form --}}
                @if($editScheduleId === $workspace->id)
                    <div class="mt-4 pt-4 border-t" style="border-color: #1e1e2e;">
                        <h4 class="text-xs font-semibold text-white mb-3">Edit Schedule</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Frequency</label>
                                <select wire:model.live="scheduleType"
                                        class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none"
                                        style="background-color: #0a0a0f; border-color: #1e1e2e;">
                                    <option value="manual">Manual</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                </select>
                            </div>
                            @if($scheduleType === 'weekly')
                                <div>
                                    <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Day</label>
                                    <select wire:model="scheduleDay"
                                            class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none"
                                            style="background-color: #0a0a0f; border-color: #1e1e2e;">
                                        <option value="monday">Monday</option>
                                        <option value="tuesday">Tuesday</option>
                                        <option value="wednesday">Wednesday</option>
                                        <option value="thursday">Thursday</option>
                                        <option value="friday">Friday</option>
                                        <option value="saturday">Saturday</option>
                                        <option value="sunday">Sunday</option>
                                    </select>
                                </div>
                            @endif
                            @if($scheduleType !== 'manual')
                                <div>
                                    <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Time</label>
                                    <input wire:model="scheduleTime" type="time"
                                           class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none"
                                           style="background-color: #0a0a0f; border-color: #1e1e2e;">
                                    @error('scheduleTime') <p class="text-xs mt-1" style="color: #e24b4a;">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button wire:click="saveSchedule"
                                    class="px-4 py-2 rounded-lg text-sm font-medium text-white"
                                    style="background-color: #ff6b2b;">
                                Save
                            </button>
                            <button wire:click="cancelSchedule"
                                    class="px-4 py-2 rounded-lg text-sm font-medium border"
                                    style="border-color: #1e1e2e; color: #6b6b8a;">
                                Cancel
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-sm" style="color: #6b6b8a;">No workspaces yet.</p>
        @endforelse
    </div>
</div>
