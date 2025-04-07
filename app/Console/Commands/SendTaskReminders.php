<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-reminders
                            {--days=1 : How many days before deadline to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for upcoming tasks';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $reminderDate = now()->addDays($days)->startOfDay();

        $tasks = Task::query()
            ->with('user')
            ->where('due_date', '>=', $reminderDate)
            ->where('due_date', '<', $reminderDate->copy()->addDay())
            ->whereNotIn('status', ['done'])
            ->cursor();

        $count = 0;

        foreach ($tasks as $task) {
            try {
                $task->user->notify(new TaskReminderNotification($task));
                $count++;
                
                $this->info("Reminder sent for task: {$task->name} (ID: {$task->id})");
                Log::info("Task reminder sent", [
                    'task_id' => $task->id,
                    'user_id' => $task->user->id,
                    'due_date' => $task->due_date
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send task reminder", [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
                $this->error("Failed to send reminder for task ID: {$task->id} - {$e->getMessage()}");
            }
        }

        $this->info("Sent {$count} task reminders for tasks due on {$reminderDate->format('Y-m-d')}");
        Log::info("Task reminders completed", ['count' => $count]);

        return 0;
    }
}