<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\QueueEntry;

class BookingService
{
    public function estimateWaitTime(Booking $booking): string
    {
        $queue = QueueEntry::where('date', $booking->visit_date)
            ->where('doctor_id', $booking->doctor_id)
            ->count();

        $estimatedMinutes = $queue * 10;

        return sprintf('%s menit', number_format($estimatedMinutes));
    }

    public function generateQueueNumber(int $doctorId, string $date): string
    {
        $count = QueueEntry::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->count();

        return sprintf('A%02d', $count + 1);
    }

    public function availableQuota(int $doctorId, string $date): int
    {
        $booked = Booking::where('doctor_id', $doctorId)
            ->where('visit_date', $date)
            ->whereIn('status', ['menunggu', 'dipanggil'])
            ->count();

        $quota = Doctor::find($doctorId)?->daily_quota ?? 20;

        return max(0, $quota - $booked);
    }
}
