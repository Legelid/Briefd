<div>
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-white">Scheduled Digests</h2>
        <p class="text-sm mt-1" style="color: #6b6b8a;">Digests queued to send</p>
    </div>

    <div class="rounded-xl border overflow-hidden" style="background-color: #0f0f18; border-color: #1e1e2e;">
        @forelse($digests as $digest)
            <div class="flex items-center gap-4 px-5 py-4 border-b last:border-0" style="border-color: #1e1e2e;">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('digests.preview', $digest) }}" class="text-sm font-medium text-white hover:underline">{{ $digest->title }}</a>
                    <p class="text-xs mt-0.5" style="color: #6b6b8a;">Scheduled {{ $digest->created_at->diffForHumans() }}</p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0" style="background-color: #071020; color: #60a5fa;">Scheduled</span>
                <a href="{{ route('digests.preview', $digest) }}" class="text-xs flex-shrink-0" style="color: #ff6b2b;">View →</a>
            </div>
        @empty
            <div class="px-5 py-12 text-center">
                <p class="text-sm" style="color: #6b6b8a;">No scheduled digests.</p>
            </div>
        @endforelse
    </div>
</div>
