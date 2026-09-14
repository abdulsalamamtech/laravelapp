<?php

namespace App\Notifications;

use App\Mail\AlertUserMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public array $data, public array $sendVia = ['mail', 'database'])
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // return ['mail'];
        $channels = $this->sendVia ?? ['mail', 'database'];

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): AlertUserMail
    {
        // return (new MailMessage)
        //     ->line('The introduction to the notification.')
        //     ->action('Notification Action', url('/'))
        //     ->line('Thank you for using our application!');

        return (new AlertUserMail($notifiable, $this->data))
            ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    // Save the custom message into the database 'data' column
    public function toDatabase(object $notifiable): array
    {
        return $this->data;
    }
}
