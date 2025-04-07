<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Daily task reminders at 9 AM
        $schedule->command('tasks:send-reminders')
            ->dailyAt('09:00')
            ->onSuccess(function () {
                Log::info('Task reminders sent successfully');
            })
            ->onFailure(function () {
                Log::error('Failed to send task reminders');
            });

        // Optional: Queue worker for processing notifications
        $schedule->command('queue:work --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/queue-worker.log'));

        // Optional: Cleanup old task shares weekly
        $schedule->command('model:prune', [
            '--model' => [\App\Models\TaskShare::class],
        ])->weekly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}