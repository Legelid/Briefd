<div>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('digests') }}" class="text-sm transition-colors" style="color: #6b6b8a;">← Back</a>
            <h2 class="text-lg font-semibold text-white">{{ $digest->title }}</h2>
            @php
                $badgeStyles = match($digest->status) {
                    'sent' => 'background-color: #071a10; color: #22c55e;',
                    'scheduled' => 'background-color: #071020; color: #60a5fa;',
                    default => 'background-color: #1e1e2e; color: #6b6b8a;',
                };
            @endphp
            <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="{{ $badgeStyles }}">{{ ucfirst($digest->status) }}</span>
        </div>

        @if($digest->status !== 'sent')
            <button wire:click="sendDigest" wire:loading.attr="disabled"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-white flex items-center gap-2"
                    style="background-color: #ff6b2b;">
                <span wire:loading.remove wire:target="sendDigest">Send Digest</span>
                <span wire:loading wire:target="sendDigest" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                </span>
            </button>
        @else
            <p class="text-sm" style="color: #6b6b8a;">Sent {{ $digest->sent_at?->diffForHumans() }}</p>
        @endif
    </div>

    @if($sentMessage)
        <div class="mb-6 px-4 py-3 rounded-lg border" style="background-color: #071a10; border-color: #22c55e; color: #22c55e;">
            {{ $sentMessage }}
        </div>
    @endif

    <div class="rounded-xl border p-6" style="background-color: #0f0f18; border-color: #1e1e2e;">
        @if($digest->content)
            <div class="prose prose-invert prose-sm max-w-none text-white" style="color: #e0e0e0;">
                {!! $digest->content !!}
            </div>
        @else
            <p class="text-sm" style="color: #6b6b8a;">No content generated yet.</p>
        @endif
    </div>
</div>
