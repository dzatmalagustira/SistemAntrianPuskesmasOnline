<?php

namespace App\Http\Controllers\Admin; // Menentukan lokasi controller ini berada di folder Admin

use App\Http\Controllers\Controller; // Mengambil controller utama Laravel
use App\Models\Booking; // Mengambil model Booking untuk mengakses tabel bookings
use App\Models\Doctor; // Mengambil model Doctor untuk mengakses tabel doctors
use App\Models\Poli; // Mengambil model Poli untuk mengakses tabel polis
use App\Models\User; // Mengambil model User untuk mengakses tabel users
use Illuminate\Http\Request; // Digunakan untuk menangkap input/filter dari user
use Illuminate\Support\Facades\DB; // Digunakan untuk query database khusus seperti COUNT(*)

class DashboardController extends Controller // Controller untuk mengatur dashboard dan laporan admin
{
    public function index(Request $request) // Menampilkan halaman dashboard admin
    {
        $today = now()->toDateString(); // Mengambil tanggal hari ini dalam format Y-m-d

        $bookings = Booking::with('doctor.poli', 'user') // Mengambil data booking beserta relasi dokter, poli, dan pasien
            ->whereDate('visit_date', $today) // Mengambil booking yang tanggal kunjungannya hari ini
            ->latest(); // Mengurutkan data booking dari yang terbaru

        $summary = [ // Membuat data ringkasan untuk card dashboard admin

            'total_pasien' => User::where('role', 'patient')->count(), // Menghitung jumlah user yang rolenya patient/pasien

            'total_booking' => Booking::count(), // Menghitung semua data booking yang ada di database

            'total_dokter' => Doctor::count(), // Menghitung semua data dokter yang ada di database

            'today_queue' => $bookings->count(), // Menghitung jumlah antrian/booking khusus hari ini
        ];

        $statusCounts = Booking::select('status', DB::raw('count(*) as total')) // Mengambil status dan menghitung jumlah tiap status
            ->groupBy('status') // Mengelompokkan data berdasarkan status booking
            ->pluck('total', 'status') // Mengubah hasil menjadi format status => total
            ->all(); // Mengambil semua hasil dalam bentuk array

        return view('admin.dashboard', [ // Membuka file view resources/views/admin/dashboard.blade.php

            'summary' => $summary, // Mengirim data ringkasan ke halaman dashboard

            'recentBookings' => $bookings->take(10)->get(), // Mengambil 10 booking terbaru hari ini

            'statusCounts' => $statusCounts, // Mengirim data jumlah booking berdasarkan status

            'polisCount' => Poli::count(), // Mengirim total jumlah poli ke dashboard
        ]);
    }

    public function reports(Request $request) // Menampilkan halaman laporan admin
    {
        $start = $request->input( // Mengambil tanggal awal dari input/filter
            'start_date', // Nama input tanggal awal
            now()->startOfMonth()->toDateString() // Jika kosong, otomatis memakai tanggal awal bulan ini
        );

        $end = $request->input( // Mengambil tanggal akhir dari input/filter
            'end_date', // Nama input tanggal akhir
            now()->toDateString() // Jika kosong, otomatis memakai tanggal hari ini
        );

        $bookings = Booking::with('doctor.poli', 'user') // Mengambil data booking beserta relasi dokter, poli, dan pasien
            ->whereBetween('visit_date', [$start, $end]); // Mengambil booking berdasarkan rentang tanggal awal sampai tanggal akhir

        $statusCounts = (clone $bookings) // Menyalin query booking agar query utama tidak berubah
            ->select('status', DB::raw('count(*) as total')) // Mengambil status dan menghitung jumlah tiap status
            ->groupBy('status') // Mengelompokkan data berdasarkan status
            ->pluck('total', 'status') // Mengubah hasil menjadi format status => total
            ->all(); // Mengambil semua hasil dalam bentuk array

        $poliCounts = (clone $bookings) // Menyalin query booking untuk menghitung jumlah booking berdasarkan poli

            ->join('doctors', 'bookings.doctor_id', '=', 'doctors.id') // Menghubungkan tabel bookings dengan tabel doctors

            ->join('polis', 'doctors.poli_id', '=', 'polis.id') // Menghubungkan tabel doctors dengan tabel polis

            ->select('polis.name', DB::raw('count(*) as total')) // Mengambil nama poli dan menghitung jumlah booking pada tiap poli

            ->groupBy('polis.name') // Mengelompokkan data berdasarkan nama poli

            ->orderByDesc('total') // Mengurutkan poli dari jumlah booking terbanyak

            ->get(); // Mengambil hasil query

        return view('admin.reports.index', [ // Membuka file view resources/views/admin/reports/index.blade.php

            'start' => $start, // Mengirim tanggal awal laporan ke view

            'end' => $end, // Mengirim tanggal akhir laporan ke view

            'totalBookings' => (clone $bookings)->count(), // Menghitung total booking pada rentang tanggal tersebut

            'completedBookings' => (clone $bookings) // Menghitung booking yang sudah selesai
                ->where('status', 'selesai') // Filter status selesai
                ->count(), // Mengambil jumlah data

            'waitingBookings' => (clone $bookings) // Menghitung booking yang masih menunggu atau sedang dipanggil
                ->whereIn('status', ['menunggu', 'dipanggil']) // Filter status menunggu dan dipanggil
                ->count(), // Mengambil jumlah data

            'cancelledBookings' => (clone $bookings) // Menghitung booking yang dibatalkan
                ->where('status', 'dibatalkan') // Filter status dibatalkan
                ->count(), // Mengambil jumlah data

            'statusCounts' => $statusCounts, // Mengirim data jumlah booking berdasarkan status ke view

            'poliCounts' => $poliCounts, // Mengirim data jumlah booking berdasarkan poli ke view

            'latestBookings' => (clone $bookings) // Mengambil data booking terbaru berdasarkan rentang tanggal
                ->latest() // Mengurutkan dari data terbaru
                ->take(10) // Mengambil maksimal 10 data
                ->get(), // Menjalankan query dan mengambil datanya
        ]);
    }
}