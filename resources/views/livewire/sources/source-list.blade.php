<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-white">RSS Sources</h2>
            <p class="text-sm mt-1" style="color: #6b6b8a;">Manage your content sources</p>
        </div>
        <button wire:click="openCreateForm"
                class="px-4 py-2 rounded-lg text-sm font-medium text-white"
                style="background-color: #ff6b2b;">
            + Add Source
        </button>
    </div>

    @if($showCreateForm)
        <div class="mb-6 p-5 rounded-xl border" style="background-color: #0f0f18; border-color: #1e1e2e;">
            <h3 class="text-sm font-semibold text-white mb-4">Add RSS Source</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Name</label>
                    <input wire:model="name" type="text" placeholder="TechCrunch"
                           class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none"
                           style="background-color: #0a0a0f; border-color: #1e1e2e;">
                    @error('name') <p class="text-xs mt-1" style="color: #e24b4a;">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">RSS Feed URL</label>
                    <input wire:model="url" type="url" placeholder="https://techcrunch.com/feed/"
                           class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none"
                           style="background-color: #0a0a0f; border-color: #1e1e2e;">
                    @error('url') <p class="text-xs mt-1" style="color: #e24b4a;">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button wire:click="create" class="px-4 py-2 rounded-lg text-sm font-medium text-white" style="background-color: #ff6b2b;">Add Source</button>
                <button wire:click="closeCreateForm" class="px-4 py-2 rounded-lg text-sm font-medium border" style="border-color: #1e1e2e; color: #6b6b8a;">Cancel</button>
            </div>
        </div>
    @endif

    <div class="rounded-xl border overflow-hidden" style="background-color: #0f0f18; border-color: #1e1e2e;">
        @forelse($sources as $source)
            <div class="flex items-center gap-4 px-5 py-4 border-b last:border-0" style="border-color: #1e1e2e;">
                <div class="w-2 h-2 rounded-full flex-shrink-0"
                     style="background-color: {{ $source->status === 'healthy' ? '#ff6b2b' : '#ef9f27' }};"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white">{{ $source->name }}</p>
                    <p class="text-xs truncate mt-0.5" style="color: #6b6b8a;">{{ $source->url }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs" style="color: #6b6b8a;">{{ $source->items_count }} items</p>
                    @if($source->last_fetched_at)
                        <p class="text-xs mt-0.5" style="color: #6b6b8a;">{{ $source->last_fetched_at->diffForHumans() }}</p>
                    @endif
                </div>
                @if($confirmDeleteId === $source->id)
                    <div class="flex items-center gap-2">
                        <button wire:click="delete" class="text-xs px-2 py-1 rounded" style="background-color: #e24b4a; color: white;">Delete</button>
                        <button wire:click="$set('confirmDeleteId', null)" class="text-xs px-2 py-1 rounded border" style="border-color: #1e1e2e; color: #6b6b8a;">Cancel</button>
                    </div>
                @else
                    <button wire:click="confirmDelete({{ $source->id }})" class="text-xs ml-2 flex-shrink-0" style="color: #6b6b8a;">Remove</button>
                @endif
            </div>
        @empty
            <div class="px-5 py-12 text-center">
                <p class="text-sm" style="color: #6b6b8a;">No sources yet. Add an RSS feed to get started.</p>
            </div>
        @endforelse
    </div>
</div>
