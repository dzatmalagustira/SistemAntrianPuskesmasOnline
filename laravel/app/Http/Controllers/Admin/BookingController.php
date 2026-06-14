<?php

namespace App\Http\Controllers\Admin; // Menentukan lokasi controller ini berada di folder Admin

use App\Http\Controllers\Controller; // Mengambil controller utama Laravel
use App\Models\Booking; // Mengambil model Booking untuk mengakses tabel bookings
use App\Notifications\BookingStatusNotification; // Notifikasi saat status booking berubah
use App\Notifications\QueueCalledNotification; // Notifikasi saat nomor antrian dipanggil
use Illuminate\Http\JsonResponse; // Digunakan untuk tipe response JSON
use Illuminate\Http\Request; // Digunakan untuk menangkap input/filter dari user

class BookingController extends Controller // Controller untuk mengatur data booking/antrian di admin
{
    public function index(Request $request) // Menampilkan halaman daftar booking/antrian admin
    {
        return view('admin.bookings.index', [ // Membuka file view admin/bookings/index.blade.php
            'bookings' => $this->bookingQuery($request) // Mengambil data booking dengan filter pencarian/status/tanggal
                ->orderByDesc('visit_date') // Mengurutkan berdasarkan tanggal kunjungan terbaru
                ->orderByDesc('created_at') // Jika tanggal sama, urutkan berdasarkan data terbaru dibuat
                ->paginate(15) // Menampilkan 15 data per halaman
                ->withQueryString(), // Agar filter tetap tersimpan saat pindah halaman

            'statuses' => ['menunggu', 'dipanggil', 'selesai', 'dibatalkan'], // Daftar status antrian
        ]);
    }

    public function updateStatus(Request $request, Booking $booking) // Mengubah status booking/antrian
    {
        $request->validate([ // Validasi input dari admin
            'status' => 'required|in:menunggu,dipanggil,selesai,dibatalkan', // Status wajib dan harus salah satu dari daftar ini
        ]);

        $booking->update(['status' => $request->status]); // Mengubah status di tabel bookings
        $booking->queue()->update(['status' => $request->status]); // Mengubah status di tabel queue/antrian juga
        $booking->loadMissing(['user', 'doctor.poli']); // Memuat relasi user, doctor, dan poli jika belum dimuat

        $booking->user->notify(new BookingStatusNotification($booking)); // Mengirim notifikasi ke pasien bahwa status berubah

        $booking->user->activityLogs()->create([ // Menyimpan riwayat aktivitas pasien
            'action' => 'Status booking diperbarui', // Nama aktivitas
            'metadata' => [ // Detail tambahan aktivitas
                'booking_id' => $booking->id, // ID booking yang diubah
                'status' => $request->status, // Status terbaru
            ],
        ]);

        return back()->with('success', 'Status antrian berhasil diperbarui.'); // Kembali ke halaman sebelumnya dengan pesan sukses
    }

    public function callNumber(Booking $booking) // Fungsi untuk memanggil nomor antrian
    {
        $booking->update(['status' => 'dipanggil']); // Mengubah status booking menjadi dipanggil
        $booking->queue()->update(['status' => 'dipanggil']); // Mengubah status queue menjadi dipanggil
        $booking->loadMissing(['user', 'doctor.poli']); // Memuat data pasien, dokter, dan poli

        $booking->user->notify(new QueueCalledNotification($booking)); // Mengirim notifikasi bahwa nomor antrian dipanggil

        $booking->user->activityLogs()->create([ // Menyimpan riwayat aktivitas pemanggilan antrian
            'action' => 'Nomor antrian dipanggil', // Nama aktivitas
            'metadata' => [ // Detail aktivitas
                'booking_id' => $booking->id, // ID booking
                'queue_number' => $booking->queue_number, // Nomor antrian pasien
                'status' => 'dipanggil', // Status terbaru
            ],
        ]);

        return back()->with( // Kembali ke halaman sebelumnya
            'success', // Jenis pesan
            'Nomor antrian '.$booking->queue_number.' dipanggil.' // Isi pesan sukses
        );
    }

    public function latest(Request $request): JsonResponse // Mengambil data booking terbaru dalam bentuk JSON
    {
        $bookings = $this->bookingQuery($request) // Mengambil data booking dengan filter yang sama
            ->orderByDesc('visit_date') // Urutkan dari tanggal kunjungan terbaru
            ->orderByDesc('created_at') // Urutkan dari data terbaru dibuat
            ->limit(15) // Ambil maksimal 15 data
            ->get() // Jalankan query dan ambil datanya
            ->map(fn (Booking $booking) => $this->bookingPayload($booking)); // Ubah data booking menjadi format array rapi

        return response()->json([ // Mengembalikan response JSON
            'bookings' => $bookings, // Isi data booking
            'generated_at' => now()->toDateTimeString(), // Waktu data dibuat
        ]);
    }

    public function exportExcel(Request $request) // Fungsi untuk export data booking
    {
        $bookings = Booking::with('user', 'doctor.poli') // Ambil booking beserta data pasien, dokter, dan poli
            ->orderByDesc('visit_date') // Urutkan berdasarkan tanggal kunjungan terbaru
            ->get(); // Ambil semua data

        $headers = [ // Header file yang akan diunduh
            'Content-Type' => 'text/csv', // Format file CSV
            'Content-Disposition' =>
                'attachment; filename="booking-export-'.now()->format('YmdHis').'.csv"', // Nama file export
        ];

        $callback = function () use ($bookings) { // Fungsi untuk menulis isi file CSV
            $file = fopen('php://output', 'w'); // Membuka output untuk menulis file

            fputcsv($file, [ // Membuat baris judul kolom CSV
                'Nomor Antrian',
                'Pasien',
                'Dokter',
                'Poli',
                'Tanggal Kunjungan',
                'Status',
            ]);

            foreach ($bookings as $booking) { // Mengulang semua data booking
                fputcsv($file, [ // Menulis setiap data booking ke file CSV
                    $booking->queue_number, // Nomor antrian
                    $booking->user->name, // Nama pasien
                    $booking->doctor->name, // Nama dokter
                    $booking->doctor->poli->name, // Nama poli
                    $booking->visit_date->format('Y-m-d'), // Tanggal kunjungan
                    ucfirst($booking->status), // Status dengan huruf awal besar
                ]);
            }

            fclose($file); // Menutup file CSV
        };

        return response()->stream($callback, 200, $headers); // Mengirim file CSV agar bisa diunduh
    }

    private function bookingQuery(Request $request) // Fungsi khusus untuk query/filter booking
    {
        $query = Booking::with(['user', 'doctor.poli']); // Ambil data booking beserta relasi pasien, dokter, dan poli

        if ($request->filled('search')) { // Jika admin mengisi kolom pencarian
            $query->where(function ($sub) use ($request) { // Membuat kondisi pencarian
                $sub->where('queue_number', 'like', '%'.$request->search.'%') // Cari berdasarkan nomor antrian
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$request->search.'%')) // Cari berdasarkan nama pasien
                    ->orWhereHas('doctor', fn ($doctor) => $doctor->where('name', 'like', '%'.$request->search.'%')); // Cari berdasarkan nama dokter
            });
        }

        if ($request->filled('status')) { // Jika admin memilih filter status
            $query->where('status', $request->status); // Filter data berdasarkan status
        }

        if ($request->filled('visit_date')) { // Jika admin memilih tanggal kunjungan
            $query->whereDate('visit_date', $request->visit_date); // Filter berdasarkan tanggal kunjungan
        }

        return $query; // Mengembalikan query yang sudah difilter
    }

    private function bookingPayload(Booking $booking): array // Mengubah data booking menjadi array untuk JSON/AJAX
    {
        return [
            'id' => $booking->id, // ID booking
            'queue_number' => $booking->queue_number, // Nomor antrian
            'patient_name' => $booking->user?->name ?? '-', // Nama pasien, jika kosong tampil "-"
            'patient_email' => $booking->user?->email ?? '-', // Email pasien
            'doctor_name' => $booking->doctor?->name ?? '-', // Nama dokter
            'poli_name' => $booking->doctor?->poli?->name ?? '-', // Nama poli
            'visit_date' => optional($booking->visit_date)->format('d M Y'), // Tanggal kunjungan dalam format mudah dibaca
            'notes' => $booking->notes ?? $booking->catatan ?? '-', // Catatan tambahan pasien
            'status' => $booking->status, // Status asli dari database
            'status_label' => ucfirst($booking->status), // Status dengan huruf awal besar
            'status_url' => route('admin.bookings.status', $booking), // URL untuk mengubah status
            'call_url' => route('admin.bookings.call', $booking), // URL untuk memanggil antrian
        ];
    }
}