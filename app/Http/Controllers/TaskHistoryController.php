<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskHistory;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class TaskHistoryController extends Controller
{
    /**
     * Display the history of changes for a specific task.
     */
    public function index(Task $task): View
    {
        Gate::authorize('view', $task);

        $histories = $task->histories()
            ->with(['changer' => function ($query) {
                $query->select('id', 'name', 'email');
            }])
            ->latest()
            ->paginate(10);

        return view('tasks.history.index', [
            'task' => $task,
            'histories' => $histories
        ]);
    }

    /**
     * Display the details of a specific history entry.
     */
    public function show(Task $task, TaskHistory $history): View
    {
        Gate::authorize('view', $history->task);

        return view('tasks.history.show', [
            'history' => $history->load(['task', 'changer' => function ($query) {
                $query->select('id', 'name', 'email');
            }])
        ]);
    }

    /**
     * Restore a task to a specific historical version.
     */
    public function restore(TaskHistory $history): RedirectResponse
    {
        Gate::authorize('update', $history->task);

        if (!$history->after) {
            return redirect()->back()
                ->with('error', 'Cannot restore from this history entry');
        }

        $task = $history->task;
        $task->fill($history->after);
        $task->save();

        // Create a new history entry for the restoration
        TaskHistory::create([
            'task_id' => $task->id,
            'changed_by' => auth()->id(),
            'before' => $task->getOriginal(),
            'after' => $history->after,
            'event_type' => 'restored',
            'change_comment' => "Restored from history #{$history->id}"
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task restored to this version');
    }

    /**
     * Compare two history versions.
     */
    public function compare(TaskHistory $first, TaskHistory $second): View
    {
        Gate::authorize('view', $first->task);
        Gate::authorize('view', $second->task);

        if ($first->task_id !== $second->task_id) {
            abort(422, 'Cannot compare histories from different tasks');
        }

        return view('tasks.history.compare', [
            'first_version' => $first,
            'second_version' => $second,
            'differences' => $this->getDifferences($first, $second)
        ]);
    }

    /**
     * Get the differences between two history entries.
     */
    protected function getDifferences(TaskHistory $first, TaskHistory $second): array
    {
        $firstData = $first->after ?? $first->before;
        $secondData = $second->after ?? $second->before;

        $differences = [];
        foreach ($firstData as $key => $value) {
            if (!isset($secondData[$key]) || $secondData[$key] != $value) {
                $differences[$key] = [
                    'from' => $value,
                    'to' => $secondData[$key] ?? null
                ];
            }
        }

        // Check for fields that exist in second but not in first
        foreach ($secondData as $key => $value) {
            if (!isset($firstData[$key])) {
                $differences[$key] = [
                    'from' => null,
                    'to' => $value
                ];
            }
        }

        return $differences;
    }
}