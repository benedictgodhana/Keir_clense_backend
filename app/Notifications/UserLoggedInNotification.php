<?php

namespace App\Notifications;
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserLoggedInNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You have been successfully logged in')
            ->line('You have been successfully logged in to our system.')
            ->action('Go to Dashboard', url('/dashboard'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            // Additional data to be sent in the notification
        ];
    }
}

