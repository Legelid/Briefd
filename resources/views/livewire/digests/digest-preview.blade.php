<div @if($digest->status === 'generating') wire:poll.2s @endif>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('digests') }}" class="text-sm transition-colors" style="color: #6b6b8a;">← Back</a>
            <h2 class="text-lg font-semibold text-white">{{ $digest->title }}</h2>
            @php
                $badgeStyles = match($digest->status) {
                    'generating' => 'background-color: #1a1400; color: #ef9f27;',
                    'sent'       => 'background-color: #071a10; color: #22c55e;',
                    'scheduled'  => 'background-color: #071020; color: #60a5fa;',
                    default      => 'background-color: #1e1e2e; color: #6b6b8a;',
                };
            @endphp
            <span class="text-xs px-2 py-0.5 rounded-full font-medium flex items-center gap-1" style="{{ $badgeStyles }}">
                @if($digest->status === 'generating')
                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                @endif
                {{ ucfirst($digest->status) }}
            </span>
        </div>

        @if($digest->status === 'sent')
            <p class="text-sm" style="color: #6b6b8a;">Sent {{ $digest->sent_at?->diffForHumans() }}</p>
        @elseif($digest->status !== 'generating')
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
        @endif
    </div>

    @if($sentMessage)
        <div class="mb-6 px-4 py-3 rounded-lg border" style="background-color: #071a10; border-color: #22c55e; color: #22c55e;">
            {{ $sentMessage }}
        </div>
    @endif

    <div class="rounded-xl border p-6" style="background-color: #0f0f18; border-color: #1e1e2e;">
        @if($digest->status === 'generating')
            <div class="flex flex-col items-center justify-center py-12 gap-4">
                <svg class="w-8 h-8 animate-spin" style="color: #ff6b2b;" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-medium text-white">Generating your digest with AI...</p>
                <p class="text-xs" style="color: #6b6b8a;">This usually takes 15–30 seconds. The page will update automatically.</p>
            </div>
        @elseif($digest->content)
            <style>
                .digest-content .digest-item { margin-bottom: 4px; }
                .digest-content .digest-item p { margin: 0 0 10px 0; line-height: 1.7; }
                .digest-content .digest-item strong a { color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 700; }
                .digest-content .digest-item strong a:hover { color: #ff6b2b; }
                .digest-content .read-more { color: #ff6b2b; font-weight: 600; font-size: 13px; text-decoration: none; }
                .digest-content hr { border: none; border-top: 1px solid #1e1e2e; margin: 20px 0; }
            </style>
            <div class="digest-content" style="color: #c0c0c0; font-size: 14px; line-height: 1.7;">
                {!! $digest->content !!}
            </div>
        @else
            <p class="text-sm" style="color: #6b6b8a;">No content generated yet.</p>
        @endif
    </div>
</div>
