<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingNotificationForUser extends Notification
{
    use Queueable;

    public $booking;
    public $employeeName;

    public function __construct($booking, $employeeName)
    {
        $this->booking = $booking;
        $this->employeeName = $employeeName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Booking Details')
            ->markdown('emails.booking_notification', [
                'booking' => $this->booking,
                'user' => $notifiable,
                'employeeName' => $this->employeeName,
            ]);
    }
}
