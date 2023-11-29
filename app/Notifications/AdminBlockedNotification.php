<?php

namespace App\Notifications;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminBlockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $admin;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
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
            ->subject('Felhasználó blokkolva')
            ->greeting('Felhasználó blokkolva')
            ->line("Kedves $notifiable->name!")
            ->line('Azért kapta ezt az email-t, mert a rendszerben '.$this->admin->name.
                ' nevű felhasználó blokkolva lett túl sok sikertelen bejelentkezés miatt.')
            ->action('Megnézem', route('admins.index'));
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
            'admin' => $this->admin,
        ];
    }
}
