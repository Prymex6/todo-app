<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'priority',
        'status',
        'due_date',
        'google_calendar_event_id',
        'sync_with_google_calendar'
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High'
    ];

    public const STATUSES = [
        'to-do' => 'To Do',
        'in-progress' => 'In Progress',
        'done' => 'Done'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(TaskShare::class);
    }

    public function activeShares(): HasMany
    {
        return $this->shares()
            ->where('expires_at', '>', now())
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhereRaw('use_count < max_uses');
            });
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TaskHistory::class);
    }

    public function googleCalendarEvent(): HasOne
    {
        return $this->hasOne(GoogleCalendarEvent::class);
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && $this->status !== 'done';
    }
}
