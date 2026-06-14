<?php

namespace App\Http\Controllers\Admin; // Menentukan lokasi controller ini berada di folder Admin

use App\Http\Controllers\Controller; // Mengambil controller utama Laravel
use App\Http\Requests\PoliRequest; // Mengambil class validasi khusus data Poli
use App\Models\Poli; // Mengambil model Poli untuk mengakses tabel polis

class PoliController extends Controller // Controller untuk mengelola data poli
{
    public function index() // Menampilkan halaman daftar poli
    {
        return view('admin.polis.index', [ // Membuka file view admin/polis/index.blade.php

            'polis' => Poli::latest() // Mengambil data poli dan mengurutkannya dari yang terbaru

                ->paginate(12), // Menampilkan 12 data per halaman
        ]);
    }

    public function create() // Menampilkan form tambah poli
    {
        return view('admin.polis.form', [ // Membuka file view admin/polis/form.blade.php

            'poli' => new Poli() // Membuat object poli kosong untuk form tambah data
        ]);
    }

    public function store(PoliRequest $request) // Menyimpan data poli baru ke database
    {
        Poli::create( // Membuat data poli baru

            $request->validated() // Menggunakan data yang sudah lolos validasi
        );

        return redirect() // Redirect ke halaman daftar poli

            ->route('admin.polis.index') // Tujuan route daftar poli

            ->with(
                'success', // Jenis pesan

                'Poli baru berhasil ditambahkan.' // Pesan sukses
            );
    }

    public function edit(Poli $poli) // Menampilkan form edit poli
    {
        return view('admin.polis.form', [ // Menggunakan form yang sama dengan tambah poli

            'poli' => $poli // Mengirim data poli yang akan diedit
        ]);
    }

    public function update(
        PoliRequest $request, // Menangkap data form yang sudah divalidasi
        Poli $poli // Data poli yang akan diperbarui
    )
    {
        $poli->update( // Mengubah data poli di database

            $request->validated() // Menggunakan data yang sudah lolos validasi
        );

        return redirect() // Redirect ke halaman daftar poli

            ->route('admin.polis.index') // Tujuan route daftar poli

            ->with(
                'success', // Jenis pesan

                'Data poli berhasil diperbarui.' // Pesan sukses
            );
    }

    public function destroy(Poli $poli) // Menghapus data poli
    {
        $poli->delete(); // Menghapus data poli dari database

        return back()->with( // Kembali ke halaman sebelumnya

            'success', // Jenis pesan

            'Poli berhasil dihapus.' // Pesan sukses setelah data dihapus
        );
    }
}