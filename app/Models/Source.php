<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['workspace_id', 'name', 'url', 'type', 'status', 'last_fetched_at', 'discord_guild_id', 'discord_channel_ids', 'discord_access_token'])]
class Source extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'last_fetched_at'     => 'datetime',
            'discord_channel_ids' => 'array',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SourceItem::class);
    }
}
