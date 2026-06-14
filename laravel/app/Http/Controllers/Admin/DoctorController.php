<?php

namespace App\Http\Controllers\Admin; // Menentukan lokasi controller ini berada di folder Admin

use App\Http\Controllers\Controller; // Mengambil controller utama Laravel
use App\Http\Requests\DoctorRequest; // Mengambil DoctorRequest untuk validasi input dokter
use App\Models\Doctor; // Mengambil model Doctor untuk mengakses tabel doctors
use App\Models\Poli; // Mengambil model Poli untuk mengakses tabel polis

class DoctorController extends Controller // Controller untuk mengelola data dokter
{
    public function index() // Menampilkan halaman daftar dokter
    {
        return view('admin.doctors.index', [ // Membuka file view admin/doctors/index.blade.php

            'doctors' => Doctor::with('poli') // Mengambil data dokter beserta data poli yang terkait
                ->latest() // Mengurutkan data dari yang terbaru
                ->paginate(12), // Menampilkan 12 data dokter per halaman
        ]);
    }

    public function create() // Menampilkan form tambah dokter
    {
        return view('admin.doctors.form', [ // Membuka file view admin/doctors/form.blade.php

            'doctor' => new Doctor(), // Membuat object dokter kosong untuk form tambah data

            'polis' => Poli::all(), // Mengambil seluruh data poli untuk pilihan dropdown
        ]);
    }

    public function store(DoctorRequest $request) // Menyimpan data dokter baru
    {
        Doctor::create( // Membuat data dokter baru di database
            $request->validated() // Mengambil data yang sudah lolos validasi
        );

        return redirect() // Redirect ke halaman daftar dokter
            ->route('admin.doctors.index') // Tujuan route daftar dokter
            ->with(
                'success', // Jenis pesan
                'Dokter baru berhasil ditambahkan.' // Pesan sukses
            );
    }

    public function edit(Doctor $doctor) // Menampilkan form edit dokter
    {
        return view('admin.doctors.form', [ // Membuka file form yang sama seperti tambah dokter

            'doctor' => $doctor, // Mengirim data dokter yang akan diedit

            'polis' => Poli::all(), // Mengambil seluruh data poli untuk dropdown pilihan
        ]);
    }

    public function update(
        DoctorRequest $request, // Menangkap data form yang sudah divalidasi
        Doctor $doctor // Data dokter yang akan diperbarui
    )
    {
        $doctor->update( // Memperbarui data dokter di database
            $request->validated() // Menggunakan data yang sudah lolos validasi
        );

        return redirect() // Redirect ke halaman daftar dokter
            ->route('admin.doctors.index') // Route daftar dokter
            ->with(
                'success', // Jenis pesan
                'Data dokter berhasil diperbarui.' // Pesan sukses
            );
    }

    public function destroy(Doctor $doctor) // Menghapus data dokter
    {
        $doctor->delete(); // Menghapus data dokter dari database

        return back()->with( // Kembali ke halaman sebelumnya
            'success', // Jenis pesan
            'Dokter berhasil dihapus.' // Pesan sukses
        );
    }
}