<div @if($hasGenerating) wire:poll.3s @endif>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-white">Digests</h2>
            <p class="text-sm mt-1" style="color: #6b6b8a;">Your newsletter digests</p>
        </div>
        <button wire:click="generateDigest" wire:loading.attr="disabled"
                class="px-4 py-2 rounded-lg text-sm font-medium text-white flex items-center gap-2"
                style="background-color: #ff6b2b;">
            <span wire:loading.remove wire:target="generateDigest">+ New Digest</span>
            <span wire:loading wire:target="generateDigest" class="flex items-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Creating...
            </span>
        </button>
    </div>

    <div class="rounded-xl border overflow-hidden" style="background-color: #0f0f18; border-color: #1e1e2e;">
        @forelse($digests as $digest)
            <div class="flex items-center gap-4 px-5 py-4 border-b last:border-0" style="border-color: #1e1e2e;">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('digests.preview', $digest) }}" class="text-sm font-medium text-white hover:underline">
                        {{ $digest->title }}
                    </a>
                    <p class="text-xs mt-0.5" style="color: #6b6b8a;">
                        {{ $digest->sent_at ? 'Sent ' . $digest->sent_at->diffForHumans() : 'Created ' . $digest->created_at->diffForHumans() }}
                    </p>
                </div>
                @php
                    $badgeStyles = match($digest->status) {
                        'generating' => 'background-color: #1a1400; color: #ef9f27;',
                        'sent'       => 'background-color: #071a10; color: #22c55e;',
                        'scheduled'  => 'background-color: #071020; color: #60a5fa;',
                        default      => 'background-color: #1e1e2e; color: #6b6b8a;',
                    };
                @endphp
                <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0 flex items-center gap-1" style="{{ $badgeStyles }}">
                    @if($digest->status === 'generating')
                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    @endif
                    {{ ucfirst($digest->status) }}
                </span>
                <a href="{{ route('digests.preview', $digest) }}" class="text-xs flex-shrink-0" style="color: #ff6b2b;">Preview</a>
                @if($confirmDeleteId === $digest->id)
                    <div class="flex items-center gap-2">
                        <button wire:click="delete" class="text-xs px-2 py-1 rounded" style="background-color: #e24b4a; color: white;">Delete</button>
                        <button wire:click="cancelDelete" class="text-xs px-2 py-1 rounded border" style="border-color: #1e1e2e; color: #6b6b8a;">Cancel</button>
                    </div>
                @else
                    <button wire:click="confirmDelete({{ $digest->id }})" class="text-xs flex-shrink-0" style="color: #6b6b8a;">Delete</button>
                @endif
            </div>
        @empty
            <div class="px-5 py-12 text-center">
                <p class="text-sm" style="color: #6b6b8a;">No digests yet. Click "New Digest" to generate one with AI.</p>
            </div>
        @endforelse
    </div>
</div>
