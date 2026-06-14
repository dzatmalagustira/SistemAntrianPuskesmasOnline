<?php

namespace App\Http\Controllers\Admin; // Menentukan lokasi controller ini berada di folder Admin

use App\Http\Controllers\Controller; // Mengambil controller utama Laravel
use App\Http\Requests\ScheduleRequest; // Mengambil class validasi khusus data jadwal
use App\Models\Doctor; // Mengambil model Doctor untuk mengakses tabel doctors
use App\Models\Schedule; // Mengambil model Schedule untuk mengakses tabel schedules

class ScheduleController extends Controller // Controller untuk mengelola jadwal dokter
{
    public function index() // Menampilkan halaman daftar jadwal dokter
    {
        return view('admin.schedules.index', [ // Membuka file view admin/schedules/index.blade.php

            'schedules' => Schedule::with('doctor.poli') // Mengambil data jadwal beserta dokter dan poli terkait

                ->latest() // Mengurutkan data dari yang terbaru

                ->paginate(12), // Menampilkan 12 data per halaman
        ]);
    }

    public function create() // Menampilkan form tambah jadwal dokter
    {
        return view('admin.schedules.form', [ // Membuka file view admin/schedules/form.blade.php

            'schedule' => new Schedule(), // Membuat object jadwal kosong untuk form tambah data

            'doctors' => Doctor::with('poli')->get(), // Mengambil seluruh dokter beserta poli untuk pilihan dropdown
        ]);
    }

    public function store(ScheduleRequest $request) // Menyimpan jadwal dokter baru
    {
        Schedule::create( // Membuat data jadwal baru di database

            $request->validated() // Menggunakan data yang sudah lolos validasi
        );

        return redirect() // Redirect ke halaman daftar jadwal

            ->route('admin.schedules.index') // Tujuan route daftar jadwal

            ->with(
                'success', // Jenis pesan

                'Jadwal dokter berhasil disimpan.' // Pesan sukses
            );
    }

    public function edit(Schedule $schedule) // Menampilkan form edit jadwal
    {
        return view('admin.schedules.form', [ // Menggunakan form yang sama seperti tambah jadwal

            'schedule' => $schedule, // Mengirim data jadwal yang akan diedit

            'doctors' => Doctor::with('poli')->get(), // Mengambil seluruh dokter untuk dropdown pilihan
        ]);
    }

    public function update(
        ScheduleRequest $request, // Menangkap data form yang sudah divalidasi
        Schedule $schedule // Data jadwal yang akan diperbarui
    )
    {
        $schedule->update( // Mengubah data jadwal di database

            $request->validated() // Menggunakan data yang sudah lolos validasi
        );

        return redirect() // Redirect ke halaman daftar jadwal

            ->route('admin.schedules.index') // Tujuan route daftar jadwal

            ->with(
                'success', // Jenis pesan

                'Jadwal berhasil diperbarui.' // Pesan sukses
            );
    }

    public function destroy(Schedule $schedule) // Menghapus jadwal dokter
    {
        $schedule->delete(); // Menghapus data jadwal dari database

        return back()->with( // Kembali ke halaman sebelumnya

            'success', // Jenis pesan

            'Jadwal berhasil dihapus.' // Pesan sukses setelah data dihapus
        );
    }
}