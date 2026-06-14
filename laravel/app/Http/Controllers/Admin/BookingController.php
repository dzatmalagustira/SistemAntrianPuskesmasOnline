<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\BookingStatusNotification;
use App\Notifications\QueueCalledNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.bookings.index', [
            'bookings' => $this->bookingQuery($request)
                ->orderByDesc('visit_date')
                ->orderByDesc('created_at')
                ->paginate(15)
                ->withQueryString(),
            'statuses' => ['menunggu', 'dipanggil', 'selesai', 'dibatalkan'],
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:menunggu,dipanggil,selesai,dibatalkan',
        ]);

        $booking->update(['status' => $request->status]);
        $booking->queue()->update(['status' => $request->status]);
        $booking->loadMissing(['user', 'doctor.poli']);

        $booking->user->notify(new BookingStatusNotification($booking));

        $booking->user->activityLogs()->create([
            'action' => 'Status booking diperbarui',
            'metadata' => [
                'booking_id' => $booking->id,
                'status' => $request->status,
            ],
        ]);

        return back()->with('success', 'Status antrian berhasil diperbarui.');
    }

    public function callNumber(Booking $booking)
    {
        $booking->update(['status' => 'dipanggil']);
        $booking->queue()->update(['status' => 'dipanggil']);
        $booking->loadMissing(['user', 'doctor.poli']);

        $booking->user->notify(new QueueCalledNotification($booking));

        $booking->user->activityLogs()->create([
            'action' => 'Nomor antrian dipanggil',
            'metadata' => [
                'booking_id' => $booking->id,
                'queue_number' => $booking->queue_number,
                'status' => 'dipanggil',
            ],
        ]);

        return back()->with(
            'success',
            'Nomor antrian '.$booking->queue_number.' dipanggil.'
        );
    }

    public function latest(Request $request): JsonResponse
    {
        $bookings = $this->bookingQuery($request)
            ->orderByDesc('visit_date')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get()
            ->map(fn (Booking $booking) => $this->bookingPayload($booking));

        return response()->json([
            'bookings' => $bookings,
            'generated_at' => now()->toDateTimeString(),
        ]);
    }

    public function exportExcel(Request $request)
    {
        $bookings = Booking::with('user', 'doctor.poli')
            ->orderByDesc('visit_date')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' =>
                'attachment; filename="booking-export-'.now()->format('YmdHis').'.csv"',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Nomor Antrian',
                'Pasien',
                'Dokter',
                'Poli',
                'Tanggal Kunjungan',
                'Status',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->queue_number,
                    $booking->user->name,
                    $booking->doctor->name,
                    $booking->doctor->poli->name,
                    $booking->visit_date->format('Y-m-d'),
                    ucfirst($booking->status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function bookingQuery(Request $request)
    {
        $query = Booking::with(['user', 'doctor.poli']);

        if ($request->filled('search')) {
            $query->where(function ($sub) use ($request) {
                $sub->where('queue_number', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$request->search.'%'))
                    ->orWhereHas('doctor', fn ($doctor) => $doctor->where('name', 'like', '%'.$request->search.'%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('visit_date')) {
            $query->whereDate('visit_date', $request->visit_date);
        }

        return $query;
    }

    private function bookingPayload(Booking $booking): array
    {
        return [
            'id' => $booking->id,
            'queue_number' => $booking->queue_number,
            'patient_name' => $booking->user?->name ?? '-',
            'patient_email' => $booking->user?->email ?? '-',
            'doctor_name' => $booking->doctor?->name ?? '-',
            'poli_name' => $booking->doctor?->poli?->name ?? '-',
            'visit_date' => optional($booking->visit_date)->format('d M Y'),
            'notes' => $booking->notes ?? $booking->catatan ?? '-',
            'status' => $booking->status,
            'status_label' => ucfirst($booking->status),
            'status_url' => route('admin.bookings.status', $booking),
            'call_url' => route('admin.bookings.call', $booking),
        ];
    }
}
