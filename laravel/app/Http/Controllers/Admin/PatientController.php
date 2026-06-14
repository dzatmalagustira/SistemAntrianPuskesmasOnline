<?php

namespace App\Http\Controllers\Admin; // Menentukan lokasi controller ini berada di folder Admin

use App\Http\Controllers\Controller; // Mengambil controller utama Laravel
use App\Models\User; // Mengambil model User untuk mengakses tabel users
use Illuminate\Http\Request; // Digunakan untuk menangkap input/filter dari user

class PatientController extends Controller // Controller untuk mengelola data pasien
{
    public function index(Request $request) // Menampilkan halaman daftar pasien
    {
        $query = User::where('role', 'patient'); // Mengambil user yang memiliki role patient

        if ($request->filled('search')) { // Jika admin mengisi kolom pencarian

            $query->where(function ($sub) use ($request) { // Membuat kondisi pencarian

                $sub->where('name', 'like', '%'.$request->search.'%') // Cari berdasarkan nama pasien

                    ->orWhere('email', 'like', '%'.$request->search.'%') // Cari berdasarkan email pasien

                    ->orWhere('phone', 'like', '%'.$request->search.'%'); // Cari berdasarkan nomor telepon pasien
            });
        }

        return view('admin.patients.index', [ // Membuka file view admin/patients/index.blade.php

            'patients' => $query // Menggunakan query pasien yang sudah difilter

                ->latest() // Mengurutkan data dari yang terbaru

                ->paginate(15) // Menampilkan 15 data per halaman

                ->withQueryString(), // Menyimpan parameter pencarian saat pindah halaman
        ]);
    }

    public function destroy(User $patient) // Menghapus data pasien
    {
        if ($patient->isAdmin()) { // Memeriksa apakah user yang dipilih adalah admin

            return back()->with( // Kembali ke halaman sebelumnya

                'error', // Jenis pesan error

                'Tidak dapat menghapus admin.' // Pesan jika mencoba menghapus admin
            );
        }

        $patient->delete(); // Menghapus data pasien dari database

        return back()->with( // Kembali ke halaman sebelumnya

            'success', // Jenis pesan sukses

            'Data pasien berhasil dihapus.' // Pesan sukses setelah pasien dihapus
        );
    }
}