<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Task Reminder: ' . $this->task->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder for your task:')
            ->line('**' . $this->task->name . '**')
            ->line('Due: ' . $this->task->due_date->format('Y-m-d H:i'))
            ->line('Priority: ' . $this->task->priority_label)
            ->action('View Task', route('tasks.show', $this->task))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_name' => $this->task->name,
            'due_date' => $this->task->due_date,
        ];
    }
}