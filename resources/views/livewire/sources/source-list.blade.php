<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-white">Sources</h2>
            <p class="text-sm mt-1" style="color: #6b6b8a;">Manage your content sources</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('auth.discord') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium text-white"
               style="background-color: #5865f2;">
                + Discord
            </a>
            <button wire:click="openCreateForm"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-white"
                    style="background-color: #ff6b2b;">
                + RSS Feed
            </button>
            <button disabled
                    class="px-4 py-2 rounded-lg text-sm font-medium border cursor-not-allowed opacity-50"
                    style="border-color: #1e1e2e; color: #6b6b8a;">
                Reddit (soon)
            </button>
        </div>
    </div>

    @if($flashMessage)
        <div class="mb-4 px-4 py-3 rounded-lg border" style="background-color: #071a10; border-color: #22c55e;">
            <p class="text-sm" style="color: #22c55e;">{{ $flashMessage }}</p>
        </div>
    @endif

    {{-- RSS Create Form --}}
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

    {{-- Discord Setup Form --}}
    @if($showDiscordForm)
        <div class="mb-6 p-5 rounded-xl border" style="background-color: #0f0f18; border-color: #5865f2;">
            @if($discordStep === 1)
                <h3 class="text-sm font-semibold text-white mb-1">Select a Discord Server</h3>
                <p class="text-xs mb-4" style="color: #6b6b8a;">Pick the server you want to monitor.</p>
                @error('discord') <p class="text-xs mb-2" style="color: #e24b4a;">{{ $message }}</p> @enderror
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($discordGuilds as $guild)
                        <button wire:click="selectGuild('{{ $guild['id'] }}')"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg border text-left transition-colors"
                                style="background-color: #0a0a0f; border-color: #1e1e2e;">
                            @if(!empty($guild['icon']))
                                <img src="https://cdn.discordapp.com/icons/{{ $guild['id'] }}/{{ $guild['icon'] }}.png"
                                     class="w-8 h-8 rounded-full flex-shrink-0" alt="">
                            @else
                                <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold"
                                     style="background-color: #5865f2; color: white;">
                                    {{ substr($guild['name'], 0, 1) }}
                                </div>
                            @endif
                            <span class="text-sm text-white">{{ $guild['name'] }}</span>
                        </button>
                    @endforeach
                </div>
                <button wire:click="cancelDiscordSetup" class="mt-4 text-xs" style="color: #6b6b8a;">Cancel</button>

            @elseif($discordStep === 2)
                <h3 class="text-sm font-semibold text-white mb-1">Select Channels</h3>
                <p class="text-xs mb-4" style="color: #6b6b8a;">Choose which text channels to monitor in <strong style="color: #ffffff;">{{ $discordGuildName }}</strong>.</p>
                @error('discord') <p class="text-xs mb-2" style="color: #e24b4a;">{{ $message }}</p> @enderror
                @if($discordChannelError)
                    <div class="mb-3 px-4 py-3 rounded-lg border" style="background-color: #1f0505; border-color: #e24b4a;">
                        <p class="text-xs" style="color: #e24b4a;">{{ $discordChannelError }}</p>
                    </div>
                @endif
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($discordChannels as $channel)
                        <label class="flex items-center gap-3 px-4 py-2 rounded-lg border cursor-pointer"
                               style="background-color: #0a0a0f; border-color: {{ in_array($channel['id'], $selectedChannelIds) ? '#5865f2' : '#1e1e2e' }};">
                            <input type="checkbox"
                                   wire:change="toggleDiscordChannel('{{ $channel['id'] }}')"
                                   @checked(in_array($channel['id'], $selectedChannelIds))
                                   class="rounded">
                            <span class="text-sm" style="color: #6b6b8a;">#</span>
                            <span class="text-sm text-white">{{ $channel['name'] }}</span>
                        </label>
                    @empty
                        <p class="text-sm" style="color: #6b6b8a;">No text channels found. Ensure the bot has access.</p>
                    @endforelse
                </div>
                <div class="flex gap-2 mt-4">
                    <button wire:click="saveDiscordSource"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-white"
                            style="background-color: #5865f2;">
                        Add Discord Source
                    </button>
                    <button wire:click="cancelDiscordSetup"
                            class="px-4 py-2 rounded-lg text-sm font-medium border"
                            style="border-color: #1e1e2e; color: #6b6b8a;">
                        Cancel
                    </button>
                </div>
            @endif
        </div>
    @endif

    {{-- Sources list --}}
    <div class="rounded-xl border overflow-hidden" style="background-color: #0f0f18; border-color: #1e1e2e;">
        @forelse($sources as $source)
            <div class="flex items-center gap-4 px-5 py-4 border-b last:border-0" style="border-color: #1e1e2e;">
                <div class="w-2 h-2 rounded-full flex-shrink-0"
                     style="background-color: {{ $source->status === 'healthy' ? '#ff6b2b' : '#ef9f27' }};"></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-white">{{ $source->name }}</p>
                        @if($source->type === 'discord')
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background-color: #1a1033; color: #7c6ef7;">Discord</span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background-color: #1f1008; color: #ff6b2b;">RSS</span>
                        @endif
                    </div>
                    <p class="text-xs truncate mt-0.5" style="color: #6b6b8a;">{{ $source->url }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs" style="color: #6b6b8a;">{{ $source->items_count }} items</p>
                    @if($source->last_fetched_at)
                        <p class="text-xs mt-0.5" style="color: #6b6b8a;">{{ $source->last_fetched_at->diffForHumans() }}</p>
                    @endif
                </div>
                <button wire:click="refreshSource({{ $source->id }})"
                        class="text-xs px-2 py-1 rounded border flex-shrink-0"
                        style="border-color: #1e1e2e; color: #6b6b8a;"
                        title="Refresh">
                    ↺
                </button>
                @if($confirmDeleteId === $source->id)
                    <div class="flex items-center gap-2">
                        <button wire:click="delete" class="text-xs px-2 py-1 rounded" style="background-color: #e24b4a; color: white;">Delete</button>
                        <button wire:click="$set('confirmDeleteId', null)" class="text-xs px-2 py-1 rounded border" style="border-color: #1e1e2e; color: #6b6b8a;">Cancel</button>
                    </div>
                @else
                    <button wire:click="confirmDelete({{ $source->id }})" class="text-xs flex-shrink-0" style="color: #6b6b8a;">Remove</button>
                @endif
            </div>
        @empty
            <div class="px-5 py-12 text-center">
                <p class="text-sm" style="color: #6b6b8a;">No sources yet. Add an RSS feed or connect Discord to get started.</p>
            </div>
        @endforelse
    </div>
</div>
