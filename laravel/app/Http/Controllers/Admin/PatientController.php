<?php

// Lokasi file controller
namespace App\Http\Controllers\Admin;

// Controller utama Laravel
use App\Http\Controllers\Controller;

// Model User
use App\Models\User;

// Untuk mengambil data request dari form
use Illuminate\Http\Request;

// Controller untuk mengelola data pasien
class PatientController extends Controller
{
    // Menampilkan daftar pasien
    public function index(Request $request)
    {
        // Ambil user yang role-nya patient
        $query = User::where('role', 'patient');

        // Jika admin mengisi kolom pencarian
        if ($request->filled('search')) {

            $query->where(function ($sub) use ($request) {

                // Cari berdasarkan nama
                $sub->where('name', 'like', '%'.$request->search.'%')

                    // Atau email
                    ->orWhere('email', 'like', '%'.$request->search.'%')

                    // Atau nomor telepon
                    ->orWhere('phone', 'like', '%'.$request->search.'%');
            });
        }

        // Kirim data ke halaman daftar pasien
        return view('admin.patients.index', [

            // Urutkan data terbaru
            // Tampilkan 15 data per halaman
            'patients' => $query
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    // Menghapus pasien
    public function destroy(User $patient)
    {
        // Cek apakah user yang dipilih adalah admin
        if ($patient->isAdmin()) {

            // Jika admin, batalkan penghapusan
            return back()->with(
                'error',
                'Tidak dapat menghapus admin.'
            );
        }

        // Hapus data pasien
        $patient->delete();

        // Kembali ke halaman sebelumnya
        return back()->with(
            'success',
            'Data pasien berhasil dihapus.'
        );
    }
}