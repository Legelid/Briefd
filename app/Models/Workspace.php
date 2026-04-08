<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'description', 'schedule_type', 'schedule_day', 'schedule_time', 'last_digest_sent_at', 'next_digest_at'])]
class Workspace extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'last_digest_sent_at' => 'datetime',
            'next_digest_at'      => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscriber::class);
    }

    public function digests(): HasMany
    {
        return $this->hasMany(Digest::class);
    }

    public function computeNextDigestAt(): ?Carbon
    {
        if ($this->schedule_type === 'manual' || empty($this->schedule_time)) {
            return null;
        }

        $time = $this->schedule_time; // HH:MM
        [$hour, $minute] = explode(':', $time);

        if ($this->schedule_type === 'daily') {
            $next = Carbon::today()->setHour((int) $hour)->setMinute((int) $minute)->setSecond(0);
            if ($next->isPast()) {
                $next->addDay();
            }
            return $next;
        }

        if ($this->schedule_type === 'weekly' && ! empty($this->schedule_day)) {
            $days = ['monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 7];
            $targetDow = $days[strtolower($this->schedule_day)] ?? 1;

            $next = Carbon::now()->startOfDay()->setHour((int) $hour)->setMinute((int) $minute)->setSecond(0);
            while ($next->dayOfWeekIso !== $targetDow || $next->isPast()) {
                $next->addDay();
            }
            return $next;
        }

        return null;
    }

    public function isDueForDigest(): bool
    {
        return $this->next_digest_at !== null && $this->next_digest_at->isPast();
    }
}
