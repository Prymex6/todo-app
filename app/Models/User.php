<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'google_access_token',
        'google_refresh_token',
        'google_expires_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function sharedTasks(): HasMany
    {
        return $this->hasMany(TaskShare::class, 'shared_by');
    }

    public function taskHistories(): HasMany
    {
        return $this->hasMany(TaskHistory::class, 'changed_by');
    }

    public function canEditTask(Task $task): bool
    {
        return $this->id === $task->user_id ||
            $task->activeShares()
            ->where('allow_editing', true)
            ->where('shared_with_email', $this->email)
            ->exists();
    }

    public function canViewTask(Task $task): bool
    {
        return $this->id === $task->user_id ||
            $task->activeShares()
            ->where('shared_with_email', $this->email)
            ->exists();
    }
}
