<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShareTaskRequest;
use App\Models\Task;
use App\Models\TaskShare;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TaskShareController extends Controller
{
    /**
     * Show the form for sharing a task
     */
    public function create(Task $task): View
    {
        Gate::authorize('share', $task);
        
        return view('tasks.shares.create', compact('task'));
    }

    /**
     * Share a task via link.
     */
    public function store(ShareTaskRequest $request, Task $task): RedirectResponse
    {
        Gate::authorize('share', $task);

        $share = $task->shares()->create([
            'shared_by' => Auth::id(),
            'token' => Str::random(64),
            'expires_at' => now()->addDays((int)$request->expiry_days),
            'max_uses' => $request->max_uses,
            'allow_editing' => $request->allow_editing,
            'shared_with_email' => $request->email,
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task shared successfully')
            ->with('share_link', route('tasks.shares.show', $share->token));
    }

    /**
     * View shared task via token.
     */
    public function show(string $token): View
    {
        $share = TaskShare::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        if ($share->max_uses !== null && $share->use_count >= $share->max_uses) {
            abort(403, 'This share link has reached its usage limit');
        }

        $share->recordAccess();

        return view('tasks.shares.show', [
            'task' => $share->task->load('user'),
            'share' => $share
        ]);
    }

    /**
     * Revoke a task share.
     */
    public function destroy(Task $task, TaskShare $share): RedirectResponse
    {
        Gate::authorize('delete', $task);

        $task = $share->task;
        $share->delete();

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Share link revoked successfully');
    }
}