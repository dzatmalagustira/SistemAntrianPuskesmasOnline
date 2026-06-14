<?php

// Lokasi file controller
namespace App\Http\Controllers\Admin;

// Controller utama Laravel
use App\Http\Controllers\Controller;

// Request khusus validasi dokter
use App\Http\Requests\DoctorRequest;

// Model Doctor
use App\Models\Doctor;

// Model Poli
use App\Models\Poli;

// Controller untuk mengelola data dokter
class DoctorController extends Controller
{
    // Menampilkan daftar dokter
    public function index()
    {
        return view('admin.doctors.index', [

            // Ambil data dokter beserta relasi poli
            // Urutkan terbaru
            // Tampilkan 12 data per halaman
            'doctors' => Doctor::with('poli')
                ->latest()
                ->paginate(12),
        ]);
    }

    // Menampilkan form tambah dokter
    public function create()
    {
        return view('admin.doctors.form', [

            // Membuat object dokter kosong
            'doctor' => new Doctor(),

            // Mengambil semua data poli
            'polis' => Poli::all(),
        ]);
    }

    // Menyimpan dokter baru ke database
    public function store(DoctorRequest $request)
    {
        // Simpan data yang sudah lolos validasi
        Doctor::create(
            $request->validated()
        );

        // Kembali ke halaman daftar dokter
        return redirect()
            ->route('admin.doctors.index')
            ->with(
                'success',
                'Dokter baru berhasil ditambahkan.'
            );
    }

    // Menampilkan form edit dokter
    public function edit(Doctor $doctor)
    {
        return view('admin.doctors.form', [

            // Data dokter yang akan diedit
            'doctor' => $doctor,

            // Semua data poli
            'polis' => Poli::all(),
        ]);
    }

    // Memperbarui data dokter
    public function update(
        DoctorRequest $request,
        Doctor $doctor
    )
    {
        // Update data dokter
        $doctor->update(
            $request->validated()
        );

        // Kembali ke daftar dokter
        return redirect()
            ->route('admin.doctors.index')
            ->with(
                'success',
                'Data dokter berhasil diperbarui.'
            );
    }

    // Menghapus dokter
    public function destroy(Doctor $doctor)
    {
        // Hapus data dokter
        $doctor->delete();

        // Kembali ke halaman sebelumnya
        return back()->with(
            'success',
            'Dokter berhasil dihapus.'
        );
    }
}