<?php

namespace App\Notifications\Task;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDoneNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;

    protected $route;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
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
            ->subject("Feladat befejezve: {$this->task->name}")
            ->greeting("Kedves {$notifiable->name}!")
            ->line("Feladat ({$this->task->name}) befejezve a beosztottad által: {$this->task->responsible->name}")
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
            'title' => "Feladat befejezve: {$this->task->name}",
            'body' => "Kedves {$notifiable->name}! Feladat ({$this->task->name}) befejezve a beosztottad által: {$this->task->responsible->name}",
            'route' => $this->route,
            'task' => TaskResource::make($this->task),
            'task_id' => $this->task->id,
            'task_name' => $this->task->name,
            'task_description' => $this->task->description,
            'task_responsible_id' => $this->task->responsible->id,
        ];
    }
}
