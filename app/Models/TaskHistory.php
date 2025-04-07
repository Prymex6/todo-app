<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'changed_by',
        'before',
        'after',
        'event_type',
        'changed_field',
        'change_comment'
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
    ];

    public const EVENT_TYPES = [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'status_changed' => 'Status Changed',
        'priority_changed' => 'Priority Changed'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getEventTypeLabelAttribute(): string
    {
        return self::EVENT_TYPES[$this->event_type] ?? $this->event_type;
    }

    public function getChangesAttribute(): array
    {
        if (!$this->before || !$this->after) {
            return [];
        }

        $changes = [];
        foreach ($this->after as $key => $value) {
            if (!isset($this->before[$key]) || $this->before[$key] != $value) {
                $changes[$key] = [
                    'from' => $this->before[$key] ?? null,
                    'to' => $value
                ];
            }
        }

        return $changes;
    }
}
