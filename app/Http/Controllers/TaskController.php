<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\TaskHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{

    protected GoogleCalendarController $googleCalendarController;

    public function __construct(GoogleCalendarController $googleCalendarController)
    {
        $this->googleCalendarController = $googleCalendarController;
    }

    /**
     * Display a listing of tasks.
     */
    public function index(): View
    {
        $query = Auth::user()->tasks()->with('user')->latest();

        if (request('status') != '') {
            $query->where('status', request('status'));
        }

        if (request('priority') != '') {
            $query->where('priority', request('priority'));
        }

        if (request('due_date') != '') {
            $query->whereDate('due_date', request('due_date'));
        }

        if (request('overdue') != '') {
            $query->where('due_date', '<', now())
                ->where('status', '!=', 'done');
        }

        return view('tasks.index', [
            'tasks' => $query->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = Auth::user()->tasks()->create($request->validated());
        $attributes = $task->getAttributes();
        unset($attributes['created_at'], $attributes['updated_at']);

        TaskHistory::create([
            'task_id' => $task->id,
            'changed_by' => Auth::id(),
            'before' => null,
            'after' => $attributes,
            'event_type' => 'created',
            'change_comment' => 'Task created'
        ]);

        if ($request->has('sync_with_google_calendar')) {
            try {
                $this->googleCalendarController->syncTask($task, 'create');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task created successfully');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): View
    {
        return view('tasks.show', [
            'task' => $task->load(['user', 'histories', 'shares']),
            'activeShares' => $task->shares()
                ->where('expires_at', '>', now())
                ->where(function ($query) {
                    $query->whereNull('max_uses')
                        ->orWhereRaw('use_count < max_uses');
                })
                ->get()
        ]);
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task): View
    {
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $originalAttributes = $task->getOriginal();
        $task->update($request->validated());
        $changedAttributes = $task->getChanges();
        
        if (empty($changedAttributes)) {
            return redirect()->route('tasks.show', $task)
            ->with('info', 'No changes were made');
        }

        foreach ($changedAttributes as $field => $newValue) {
            if (in_array($field, ['created_at', 'updated_at'])) {
                continue;
            }

            TaskHistory::create([
                'task_id' => $task->id,
                'changed_by' => Auth::id(),
                'before' => [$field => $originalAttributes[$field] ?? null],
                'after' => [$field => $newValue],
                'event_type' => 'updated',
                'changed_field' => $field,
                'change_comment' => $request->input('change_comment', "Updated {$field}")
            ]);
        }

        if ($task->sync_with_google_calendar && $task->google_calendar_event_id) {
            try {
                app(GoogleCalendarController::class)->syncTask($task, 'update');
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        } else {
            app(GoogleCalendarController::class)->syncTask($task, 'create');
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task): RedirectResponse
    {
        if ($task->google_calendar_event_id) {
            try {
                $this->googleCalendarController->deleteEvent($task);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        TaskHistory::create([
            'task_id' => $task->id,
            'changed_by' => Auth::id(),
            'before' => $task->getOriginal(),
            'after' => null,
            'event_type' => 'deleted',
            'changed_field' => null,
            'change_comment' => 'Task deleted'
        ]);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully');
    }

    /**
     * Display task history.
     */
    public function history(Task $task): View
    {
        return view('tasks.history', [
            'task' => $task,
            'histories' => $task->histories()->latest()->paginate(10)
        ]);
    }
}
