<?php

namespace App\Notifications\Task;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCommentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;

    public $comment;

    protected $route;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task, TaskComment $taskComment)
    {
        $this->task = $task;
        $this->comment = $taskComment;
        $this->route = route('tasks.show', $task->id);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable->email_notifications) {
            return ['mail', 'database'];
        }

        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Új megjegyzés érkezett a feladatra: {$this->task->name}")
            ->greeting("Kedves {$notifiable->name}!")
            ->line("Új megjegyzés érkezett tőle: {$this->comment->admin->name}")
            ->action('Megnézem', $this->route);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => "Új megjegyzés érkezett a feladatra: {$this->task->name}",
            'body' => "Kedves {$notifiable->name}! Új megjegyzés érkezett tőle: {$this->comment->admin->name}",
            'route' => $this->route,
            'task' => TaskResource::make($this->task),
            'task_id' => $this->task->id,
            'task_name' => $this->task->name,
            'task_description' => $this->task->description,
            'task_responsible_id' => $this->task->responsible->id,
        ];
    }
}
