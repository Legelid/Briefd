<?php

namespace App\Livewire\Sources;

use App\Jobs\FetchDiscordSources;
use App\Jobs\FetchRssSources;
use App\Models\Source;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class SourceList extends Component
{
    public string $name = '';
    public string $url = '';
    public bool $showCreateForm = false;
    public ?int $confirmDeleteId = null;
    public ?string $flashMessage = null;

    // Discord setup state
    public array $discordGuilds = [];
    public int $discordStep = 1;
    public string $selectedGuildId = '';
    public array $selectedChannelIds = [];
    public array $discordChannels = [];
    public string $discordGuildName = '';
    public bool $showDiscordForm = false;
    public ?string $discordChannelError = null;

    public function mount(): void
    {
        if (request()->query('discord') && session('discord_guilds')) {
            $this->discordGuilds = session('discord_guilds');
            $this->showDiscordForm = true;
        }
    }

    public function openCreateForm(): void
    {
        $this->showCreateForm = true;
        $this->showDiscordForm = false;
    }

    public function closeCreateForm(): void
    {
        $this->showCreateForm = false;
        $this->reset('name', 'url');
    }

    public function create(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'url'  => 'required|url|max:500',
        ]);

        $workspace = Auth::user()->currentWorkspace();

        $workspace->sources()->create([
            'name'   => $this->name,
            'url'    => $this->url,
            'type'   => 'rss',
            'status' => 'healthy',
        ]);

        $this->reset('name', 'url', 'showCreateForm');
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function delete(): void
    {
        $workspace = Auth::user()->currentWorkspace();
        $source    = $workspace->sources()->findOrFail($this->confirmDeleteId);
        $source->delete();
        $this->confirmDeleteId = null;
    }

    public function refreshSource(int $sourceId): void
    {
        $workspace = Auth::user()->currentWorkspace();
        $source    = $workspace->sources()->findOrFail($sourceId);

        if ($source->type === 'discord') {
            FetchDiscordSources::dispatch();
        } else {
            FetchRssSources::dispatch();
        }

        $this->flashMessage = 'Refresh queued.';
    }

    // Discord: pick guild
    public function selectGuild(string $guildId): void
    {
        $this->selectedGuildId  = $guildId;
        $guild = collect($this->discordGuilds)->firstWhere('id', $guildId);
        $this->discordGuildName = $guild['name'] ?? 'Unknown Server';
        $this->discordStep      = 2;
        $this->fetchDiscordChannels();
    }

    public function fetchDiscordChannels(): void
    {
        if (! $this->selectedGuildId) return;

        $botToken = config('services.discord.bot_token');
        if (! $botToken) {
            $this->discordChannelError = 'Discord bot token is not configured.';
            return;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bot ' . $botToken,
        ])->get("https://discord.com/api/v10/guilds/{$this->selectedGuildId}/channels");

        if ($response->successful()) {
            $this->discordChannels = collect($response->json())
                ->filter(fn ($ch) => ($ch['type'] ?? -1) === 0)
                ->sortBy('position')
                ->values()
                ->toArray();
            $this->discordChannelError = null;
        } else {
            $this->discordChannelError = 'Could not fetch channels (HTTP ' . $response->status() . '). Ensure the bot is in this server.';
        }
    }

    public function toggleDiscordChannel(string $channelId): void
    {
        if (in_array($channelId, $this->selectedChannelIds)) {
            $this->selectedChannelIds = array_values(array_filter(
                $this->selectedChannelIds,
                fn ($id) => $id !== $channelId
            ));
        } else {
            $this->selectedChannelIds[] = $channelId;
        }
    }

    public function saveDiscordSource(): void
    {
        if (empty($this->selectedGuildId) || empty($this->selectedChannelIds)) {
            $this->addError('discord', 'Please select at least one channel.');
            return;
        }

        $workspace = Auth::user()->currentWorkspace();

        $workspace->sources()->create([
            'name'                 => $this->discordGuildName,
            'url'                  => 'https://discord.com/channels/' . $this->selectedGuildId,
            'type'                 => 'discord',
            'status'               => 'healthy',
            'discord_guild_id'     => $this->selectedGuildId,
            'discord_channel_ids'  => $this->selectedChannelIds,
            'discord_access_token' => session('discord_token'),
        ]);

        session()->forget(['discord_guilds', 'discord_token']);

        $this->reset('showDiscordForm', 'discordGuilds', 'discordStep', 'selectedGuildId', 'selectedChannelIds', 'discordChannels', 'discordGuildName');
        $this->flashMessage = 'Discord source added.';
    }

    public function cancelDiscordSetup(): void
    {
        session()->forget(['discord_guilds', 'discord_token']);
        $this->reset('showDiscordForm', 'discordGuilds', 'discordStep', 'selectedGuildId', 'selectedChannelIds', 'discordChannels', 'discordGuildName');
    }

    public function render()
    {
        $workspace = Auth::user()->currentWorkspace();
        return view('livewire.sources.source-list', [
            'sources' => $workspace ? $workspace->sources()->withCount('items')->latest()->get() : collect(),
        ])->layout('layouts.app', ['title' => 'Sources']);
    }
}
