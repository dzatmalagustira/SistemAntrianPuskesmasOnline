<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Poli;
use App\Models\QueueEntry;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(BookingService $service)
    {
        $user = Auth::user();

        $upcoming = $user->bookings()
            ->whereIn('status', ['menunggu', 'dipanggil'])
            ->with('doctor.poli')
            ->orderBy('visit_date')
            ->get();

        $history = $user->bookings()
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->with('doctor.poli')
            ->latest()
            ->limit(10)
            ->get();

        return view('patient.dashboard', [
            'summary' => [
                'active_bookings' => $upcoming->count(),
                'total_history' => $history->count(),
                'next_visit' => optional($upcoming->first())->visit_date?->format('d M Y'),
            ],
            'upcoming' => $upcoming,
            'history' => $history,
            'policies' => Poli::with('doctors')->get(),
            'estimated_wait' => optional($upcoming->first())->estimated_wait,
            'calledNotification' => $user->unreadNotifications()
                ->where('type', \App\Notifications\QueueCalledNotification::class)
                ->latest()
                ->first(),
        ]);
    }

    public function create()
    {
        return view('patient.bookings.create', [
            'polis' => Poli::with('doctors.schedules')->get(),
            'doctors' => Doctor::with(['poli', 'schedules'])->get(),
        ]);
    }

    public function store(BookingRequest $request, BookingService $service)
    {
        $validated = $request->validated();
        $doctor = Doctor::with('poli')->findOrFail($validated['doctor_id']);

        if ((int) $doctor->poli_id !== (int) $validated['poli_id']) {
            return back()->withInput()->with('error', 'Dokter yang dipilih tidak sesuai dengan poli.');
        }

        $weekday = strtolower(Carbon::parse($validated['visit_date'])->englishDayOfWeek);
        $hasSchedule = $doctor->schedules()
            ->where('weekday', $weekday)
            ->exists();

        if (! $hasSchedule) {
            return back()
                ->withInput()
                ->with('error', 'Dokter tidak praktik pada tanggal yang dipilih. Silakan pilih tanggal sesuai jadwal dokter.');
        }

        $quota = $service->availableQuota($validated['doctor_id'], $validated['visit_date']);

        if ($quota <= 0) {
            return back()->with('error', 'Kuota antrian sudah penuh untuk hari tersebut. Silakan pilih tanggal lain.');
        }

        $queueNumber = $service->generateQueueNumber($validated['doctor_id'], $validated['visit_date']);
        $estimatedWait = $service->estimateWaitTime(new Booking([
            'doctor_id' => $validated['doctor_id'],
            'visit_date' => $validated['visit_date'],
        ]));

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'doctor_id' => $validated['doctor_id'],
            'visit_date' => $validated['visit_date'],
            'queue_number' => $queueNumber,
            'status' => 'menunggu',
            'notes' => $validated['notes'] ?? null,
            'estimated_wait' => $estimatedWait,
        ]);

        QueueEntry::create([
            'booking_id' => $booking->id,
            'doctor_id' => $booking->doctor_id,
            'user_id' => Auth::id(),
            'date' => $booking->visit_date,
            'number' => $booking->queue_number,
            'status' => 'menunggu',
        ]);

        Auth::user()->activityLogs()->create([
            'action' => 'Booking dibuat',
            'metadata' => ['booking_id' => $booking->id],
        ]);

        Auth::user()->notify(new \App\Notifications\BookingStatusNotification($booking));

        return redirect()->route('patient.dashboard')->with('success', 'Booking berhasil dibuat. Nomor antrian Anda: '.$queueNumber);
    }
    public function show(Booking $booking)
    {
        if ((int) $booking->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $booking->load(['user', 'doctor.poli', 'queue']);

        return view('patient.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ((int) $booking->user_id !== (int) auth()->id()) {
            abort(403);
        }

        if (! in_array($booking->status, ['menunggu', 'dipanggil'], true)) {
            return redirect()
                ->route('patient.dashboard')
                ->with('error', 'Booking ini tidak bisa dibatalkan karena statusnya sudah '.ucfirst($booking->status).'.');
        }

        $booking->update(['status' => 'dibatalkan']);
        $booking->queue()->update(['status' => 'dibatalkan']);

        Auth::user()->activityLogs()->create([
            'action' => 'Booking dibatalkan',
            'metadata' => ['booking_id' => $booking->id],
        ]);

        Auth::user()->notify(new \App\Notifications\BookingStatusNotification($booking));

        return redirect()
            ->route('patient.dashboard')
            ->with('success', 'Booking berhasil dibatalkan.');
    }
}
