<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\TaskShare;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskSharedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Task $task,
        public TaskShare $share
    ) {}

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Task Shared With You: ' . $this->task->name)
            ->markdown('emails.task-shared', [
                'task' => $this->task,
                'share' => $this->share,
                'shareLink' => route('tasks.shares.show', $this->share->token),
                'expiryDate' => $this->share->expires_at->format('Y-m-d H:i'),
                'allowEditing' => $this->share->allow_editing,
                'ownerName' => $this->task->user->name,
            ]);
    }
}