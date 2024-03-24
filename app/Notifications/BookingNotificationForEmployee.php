<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingNotificationForEmployee extends Notification
{
    use Queueable;

    protected $booking;
    protected $employeeName;

    /**
     * Create a new notification instance.
     *
     * @param mixed $booking
     * @param string $employeeName
     */
    public function __construct($booking, $employeeName)
    {
        $this->booking = $booking;
        $this->employeeName = $employeeName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Booking')
            ->markdown('emails.employee_booking_notification', [
                'booking' => $this->booking,
                'employeeName' => $this->employeeName,
            ]);
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
}
