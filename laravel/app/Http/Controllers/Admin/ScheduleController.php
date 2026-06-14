<?php

// Namespace lokasi file controller
namespace App\Http\Controllers\Admin;

// Controller bawaan Laravel
use App\Http\Controllers\Controller;

// Request khusus validasi jadwal
use App\Http\Requests\ScheduleRequest;

// Model Doctor
use App\Models\Doctor;

// Model Schedule
use App\Models\Schedule;

// Controller untuk mengelola jadwal dokter
class ScheduleController extends Controller
{
    // Menampilkan daftar jadwal dokter
    public function index()
    {
        return view('admin.schedules.index', [

            // Mengambil data jadwal beserta relasi dokter dan poli
            // latest() = data terbaru di atas
            // paginate(12) = 12 data per halaman
            'schedules' => Schedule::with('doctor.poli')
                ->latest()
                ->paginate(12),
        ]);
    }

    // Menampilkan form tambah jadwal
    public function create()
    {
        return view('admin.schedules.form', [

            // Objek jadwal kosong
            'schedule' => new Schedule(),

            // Mengambil seluruh dokter beserta poli
            // untuk dropdown pilihan dokter
            'doctors' => Doctor::with('poli')->get(),
        ]);
    }

    // Menyimpan jadwal baru
    public function store(ScheduleRequest $request)
    {
        // Menyimpan data yang sudah lolos validasi
        Schedule::create(
            $request->validated()
        );

        return redirect()
            ->route('admin.schedules.index')
            ->with(
                'success',
                'Jadwal dokter berhasil disimpan.'
            );
    }

    // Menampilkan form edit jadwal
    public function edit(Schedule $schedule)
    {
        return view('admin.schedules.form', [

            // Data jadwal yang dipilih
            'schedule' => $schedule,

            // Daftar dokter untuk dropdown
            'doctors' => Doctor::with('poli')->get(),
        ]);
    }

    // Memperbarui jadwal
    public function update(
        ScheduleRequest $request,
        Schedule $schedule
    )
    {
        // Update data jadwal
        $schedule->update(
            $request->validated()
        );

        return redirect()
            ->route('admin.schedules.index')
            ->with(
                'success',
                'Jadwal berhasil diperbarui.'
            );
    }

    // Menghapus jadwal
    public function destroy(Schedule $schedule)
    {
        // Hapus jadwal dari database
        $schedule->delete();

        return back()->with(
            'success',
            'Jadwal berhasil dihapus.'
        );
    }
}