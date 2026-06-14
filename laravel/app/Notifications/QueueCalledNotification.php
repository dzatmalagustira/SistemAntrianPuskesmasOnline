<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QueueCalledNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
        $this->booking->loadMissing('doctor.poli');
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $poliName = $this->booking->doctor?->poli?->name ?? 'poli tujuan';

        return [
            'title' => 'Nomor Antrian Dipanggil',
            'message' => "Nomor antrian {$this->booking->queue_number} sedang dipanggil. Silakan menuju {$poliName}.",
            'booking_id' => $this->booking->id,
            'queue_number' => $this->booking->queue_number,
            'status' => 'dipanggil',
            'poli' => $poliName,
            'url' => route('patient.booking.show', $this->booking),
        ];
    }
}
