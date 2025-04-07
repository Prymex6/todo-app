<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TaskShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'shared_by',
        'token',
        'expires_at',
        'max_uses',
        'allow_editing',
        'shared_with_email'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'allow_editing' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->token = $model->token ?? Str::random(64);
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function hasReachedUsageLimit(): bool
    {
        return $this->max_uses !== null && $this->use_count >= $this->max_uses;
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->hasReachedUsageLimit();
    }

    public function recordAccess(): void
    {
        if (!$this->first_accessed_at) {
            $this->first_accessed_at = now();
        }
        $this->last_accessed_at = now();
        $this->use_count++;
        $this->save();
    }
}
