<?php

// Namespace menunjukkan lokasi file controller ini
namespace App\Http\Controllers\Admin;

// Memanggil Controller bawaan Laravel
use App\Http\Controllers\Controller;

// Memanggil class validasi khusus untuk data Poli
use App\Http\Requests\PoliRequest;

// Memanggil model Poli untuk berinteraksi dengan database
use App\Models\Poli;

// Controller untuk mengelola data Poli
class PoliController extends Controller
{
    // Menampilkan daftar semua poli
    public function index()
    {
        return view('admin.polis.index', [

            // Mengambil data poli dari database
            // latest() = urutkan data terbaru
            // paginate(12) = tampilkan 12 data per halaman
            'polis' => Poli::latest()->paginate(12),
        ]);
    }

    // Menampilkan form tambah poli
    public function create()
    {
        return view('admin.polis.form', [

            // Membuat objek poli kosong
            // Digunakan agar form tambah dan edit bisa memakai view yang sama
            'poli' => new Poli()
        ]);
    }

    // Menyimpan data poli baru ke database
    public function store(PoliRequest $request)
    {
        // Menyimpan data yang sudah lolos validasi
        Poli::create(
            $request->validated()
        );

        // Redirect ke halaman daftar poli
        // dengan pesan sukses
        return redirect()
            ->route('admin.polis.index')
            ->with(
                'success',
                'Poli baru berhasil ditambahkan.'
            );
    }

    // Menampilkan form edit poli
    public function edit(Poli $poli)
    {
        return view('admin.polis.form', [

            // Mengirim data poli yang akan diedit
            'poli' => $poli
        ]);
    }

    // Memperbarui data poli
    public function update(
        PoliRequest $request,
        Poli $poli
    )
    {
        // Update data poli berdasarkan input form
        $poli->update(
            $request->validated()
        );

        // Kembali ke halaman daftar poli
        // dengan pesan sukses
        return redirect()
            ->route('admin.polis.index')
            ->with(
                'success',
                'Data poli berhasil diperbarui.'
            );
    }

    // Menghapus data poli
    public function destroy(Poli $poli)
    {
        // Hapus data poli dari database
        $poli->delete();

        // Kembali ke halaman sebelumnya
        // dengan pesan sukses
        return back()->with(
            'success',
            'Poli berhasil dihapus.'
        );
    }
}