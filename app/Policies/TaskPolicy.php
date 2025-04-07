<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->user_id || 
               $task->activeShares()
                   ->where('shared_with_email', $user->email)
                   ->exists();
    }

    /**
     * Determine whether the user can create tasks.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id || 
               $task->activeShares()
                   ->where('shared_with_email', $user->email)
                   ->where('allow_editing', true)
                   ->exists();
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine whether the user can share the task.
     */
    public function share(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine whether the user can view the task history.
     */
    public function viewHistory(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    /**
     * Determine whether the user can restore the task from history.
     */
    public function restore(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }
}