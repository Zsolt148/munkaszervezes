<?php

namespace App\Notifications;

use App\Helpers\Interfaces\MentionInterface;
use App\Http\Resources\TaskCommentResource;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Model $model;

    protected string $title;

    protected string $body;

    protected string $route;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(MentionInterface $model)
    {
        $this->model = $model;

        $this->title = $model->mentionTitle();
        $this->body = $model->mentionBody();
        $this->route = $model->mentionRoute();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(object $notifiable)
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
    public function toMail(object $notifiable)
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting("Kedves $notifiable->name!")
            ->line($this->body)
            ->action('Megnézem', $this->route);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray(object $notifiable)
    {
        $data = [
            'title' => $this->title,
            'body' => $this->body,
            'route' => $this->route,
        ];

        if ($this->model instanceof Task) {
            $this->model->load('responsible');
            $data['task'] = TaskResource::make($this->model);
        }

        if ($this->model instanceof TaskComment) {
            $this->model->task->load('responsible');
            $data['task'] = TaskResource::make($this->model->task);
            $data['task_comment'] = TaskCommentResource::make($this->model);
        }

        return $data;
    }
}
