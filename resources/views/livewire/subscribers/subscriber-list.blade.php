<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-white">Subscribers</h2>
            <p class="text-sm mt-1" style="color: #6b6b8a;">{{ $totalCount }} total</p>
        </div>
        @if(!$atLimit)
            <button wire:click="openCreateForm"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-white"
                    style="background-color: #ff6b2b;">
                + Add Subscriber
            </button>
        @endif
    </div>

    @if($atLimit)
        <div class="mb-6 p-4 rounded-xl border" style="background-color: #1f1008; border-color: #ff6b2b;">
            <p class="text-sm font-medium" style="color: #ff6b2b;">Free plan: 50 subscriber limit reached</p>
            <p class="text-sm mt-1" style="color: #6b6b8a;">Upgrade to add more subscribers.</p>
            <button class="mt-3 px-3 py-1.5 rounded-lg text-xs font-medium text-white opacity-50 cursor-not-allowed" style="background-color: #ff6b2b;" disabled>Upgrade Plan</button>
        </div>
    @endif

    @if($showCreateForm)
        <div class="mb-6 p-5 rounded-xl border" style="background-color: #0f0f18; border-color: #1e1e2e;">
            <h3 class="text-sm font-semibold text-white mb-4">Add Subscriber</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Name</label>
                    <input wire:model="name" type="text"
                           class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none"
                           style="background-color: #0a0a0f; border-color: #1e1e2e;">
                    @error('name') <p class="text-xs mt-1" style="color: #e24b4a;">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: #6b6b8a;">Email</label>
                    <input wire:model="email" type="email"
                           class="w-full rounded-lg text-sm text-white border px-3 py-2 focus:outline-none"
                           style="background-color: #0a0a0f; border-color: #1e1e2e;">
                    @error('email') <p class="text-xs mt-1" style="color: #e24b4a;">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button wire:click="create" class="px-4 py-2 rounded-lg text-sm font-medium text-white" style="background-color: #ff6b2b;">Add</button>
                <button wire:click="closeCreateForm" class="px-4 py-2 rounded-lg text-sm font-medium border" style="border-color: #1e1e2e; color: #6b6b8a;">Cancel</button>
            </div>
        </div>
    @endif

    <div class="rounded-xl border overflow-hidden" style="background-color: #0f0f18; border-color: #1e1e2e;">
        @forelse($subscribers as $subscriber)
            <div class="flex items-center gap-4 px-5 py-3 border-b last:border-0" style="border-color: #1e1e2e;">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                     style="background-color: #1e1e2e;">
                    {{ strtoupper(substr($subscriber->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white">{{ $subscriber->name }}</p>
                    <p class="text-xs" style="color: #6b6b8a;">{{ $subscriber->email }}</p>
                </div>
                <p class="text-xs flex-shrink-0" style="color: #6b6b8a;">{{ $subscriber->created_at->diffForHumans() }}</p>
                @if($confirmDeleteId === $subscriber->id)
                    <div class="flex items-center gap-2">
                        <button wire:click="delete" class="text-xs px-2 py-1 rounded" style="background-color: #e24b4a; color: white;">Remove</button>
                        <button wire:click="$set('confirmDeleteId', null)" class="text-xs px-2 py-1 rounded border" style="border-color: #1e1e2e; color: #6b6b8a;">Cancel</button>
                    </div>
                @else
                    <button wire:click="confirmDelete({{ $subscriber->id }})" class="text-xs flex-shrink-0" style="color: #6b6b8a;">Remove</button>
                @endif
            </div>
        @empty
            <div class="px-5 py-12 text-center">
                <p class="text-sm" style="color: #6b6b8a;">No subscribers yet.</p>
            </div>
        @endforelse
    </div>

    @if($subscribers instanceof \Illuminate\Pagination\LengthAwarePaginator && $subscribers->hasPages())
        <div class="mt-4">{{ $subscribers->links() }}</div>
    @endif
</div>
