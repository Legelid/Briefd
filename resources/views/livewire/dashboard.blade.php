<div>
    {{-- Source health bar --}}
    @if($this->unhealthySources > 0)
        <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-lg border" style="background-color: #1f0e00; border-color: #ef9f27;">
            <svg class="w-4 h-4 flex-shrink-0" style="color: #ef9f27;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="text-sm" style="color: #ef9f27;">{{ $this->unhealthySources }} source(s) need attention</span>
            <a href="{{ route('sources') }}" class="ml-auto text-xs font-medium" style="color: #ef9f27;">View sources →</a>
        </div>
    @else
        <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-lg border" style="background-color: #071a10; border-color: #22c55e;">
            <svg class="w-4 h-4 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm text-green-500">All sources healthy</span>
        </div>
    @endif

    {{-- Next digest banner --}}
    @if($this->nextDigest)
        <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-lg border" style="background-color: #071020; border-color: #3b5cf6;">
            <svg class="w-4 h-4 flex-shrink-0" style="color: #60a5fa;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm" style="color: #60a5fa;">Next digest: <strong>{{ $this->nextDigest }}</strong></span>
            <a href="{{ route('workspaces') }}" class="ml-auto text-xs font-medium" style="color: #60a5fa;">Edit schedule →</a>
        </div>
    @endif

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl border p-5" style="background-color: #0f0f18; border-color: #1e1e2e;">
            <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: #6b6b8a;">Subscribers</p>
            <p class="text-2xl font-bold text-white">{{ $this->stats['subscribers'] }}</p>
        </div>
        <div class="rounded-xl border p-5" style="background-color: #0f0f18; border-color: #1e1e2e;">
            <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: #6b6b8a;">Digests Sent</p>
            <p class="text-2xl font-bold text-white">{{ $this->stats['digests_sent'] }}</p>
        </div>
        <div class="rounded-xl border p-5" style="background-color: #0f0f18; border-color: #1e1e2e;">
            <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: #6b6b8a;">Active Sources</p>
            <p class="text-2xl font-bold text-white">{{ $this->stats['active_sources'] }}</p>
        </div>
        <div class="rounded-xl border p-5" style="background-color: #0f0f18; border-color: #1e1e2e;">
            <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: #6b6b8a;">Avg Open Rate</p>
            <p class="text-2xl font-bold text-white">{{ $this->stats['avg_open_rate'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Digests --}}
        <div class="rounded-xl border p-5" style="background-color: #0f0f18; border-color: #1e1e2e;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white">Recent Digests</h3>
                <a href="{{ route('digests') }}" class="text-xs" style="color: #ff6b2b;">View all →</a>
            </div>
            @forelse($this->recentDigests as $digest)
                <div class="flex items-center justify-between py-2 border-b last:border-0" style="border-color: #1e1e2e;">
                    <div>
                        <p class="text-sm text-white">{{ $digest->title }}</p>
                        <p class="text-xs mt-0.5" style="color: #6b6b8a;">{{ $digest->created_at->diffForHumans() }}</p>
                    </div>
                    @php
                        $badgeStyles = match($digest->status) {
                            'sent' => 'background-color: #071a10; color: #22c55e;',
                            'scheduled' => 'background-color: #071020; color: #60a5fa;',
                            default => 'background-color: #1e1e2e; color: #6b6b8a;',
                        };
                    @endphp
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="{{ $badgeStyles }}">
                        {{ ucfirst($digest->status) }}
                    </span>
                </div>
            @empty
                <p class="text-sm" style="color: #6b6b8a;">No digests yet. <a href="{{ route('digests') }}" style="color: #ff6b2b;">Create one →</a></p>
            @endforelse
        </div>

        {{-- Connected Sources --}}
        <div class="rounded-xl border p-5" style="background-color: #0f0f18; border-color: #1e1e2e;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white">Connected Sources</h3>
                <a href="{{ route('sources') }}" class="text-xs" style="color: #ff6b2b;">Manage →</a>
            </div>
            @forelse($this->sources as $source)
                <div class="flex items-center gap-3 py-2 border-b last:border-0" style="border-color: #1e1e2e;">
                    <div class="w-2 h-2 rounded-full flex-shrink-0"
                         style="background-color: {{ $source->status === 'healthy' ? '#ff6b2b' : '#ef9f27' }};"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm text-white truncate">{{ $source->name }}</p>
                            @if($source->type === 'discord')
                                <span class="text-xs px-1.5 py-0.5 rounded font-semibold flex-shrink-0" style="background-color: #1a1033; color: #7c6ef7;">Discord</span>
                            @else
                                <span class="text-xs px-1.5 py-0.5 rounded font-semibold flex-shrink-0" style="background-color: #1f1008; color: #ff6b2b;">RSS</span>
                            @endif
                        </div>
                        <p class="text-xs" style="color: #6b6b8a;">{{ $source->items_count }} items</p>
                    </div>
                </div>
            @empty
                <p class="text-sm" style="color: #6b6b8a;">No sources yet. <a href="{{ route('sources') }}" style="color: #ff6b2b;">Add one →</a></p>
            @endforelse
        </div>
    </div>
</div>
