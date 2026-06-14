<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Status Booking Diperbarui',
            'message' => "Booking Anda untuk Dr. {$this->booking->doctor->name} pada {$this->booking->visit_date} sekarang berstatus {$this->booking->status}.",
            'booking_id' => $this->booking->id,
        ];
    }

}
