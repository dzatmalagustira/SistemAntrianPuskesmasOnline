<?php

// Lokasi file controller ini
namespace App\Http\Controllers\Admin;

// Controller utama bawaan Laravel
use App\Http\Controllers\Controller;

// Model yang dipakai untuk mengambil data dari database
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Poli;
use App\Models\User;

// Untuk menangkap input/filter dari request
use Illuminate\Http\Request;

// Untuk menggunakan query mentah seperti COUNT(*)
use Illuminate\Support\Facades\DB;

// Controller dashboard admin
class DashboardController extends Controller
{
    // Method untuk menampilkan halaman dashboard admin
    public function index(Request $request)
    {
        // Mengambil tanggal hari ini dalam format Y-m-d
        $today = now()->toDateString();

        // Mengambil data booking hari ini
        // Sekaligus mengambil relasi doctor, poli, dan user
        $bookings = Booking::with('doctor.poli', 'user')
            ->whereDate('visit_date', $today)
            ->latest();

        // Data ringkasan untuk card dashboard
        $summary = [

            // Menghitung total user yang role-nya patient/pasien
            'total_pasien' => User::where('role', 'patient')->count(),

            // Menghitung seluruh data booking
            'total_booking' => Booking::count(),

            // Menghitung total dokter
            'total_dokter' => Doctor::count(),

            // Menghitung jumlah antrian khusus hari ini
            'today_queue' => $bookings->count(),
        ];

        // Menghitung jumlah booking berdasarkan status
        // Contoh hasil:
        // menunggu = 5, dipanggil = 2, selesai = 10
        $statusCounts = Booking::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        // Mengirim data ke view admin.dashboard
        return view('admin.dashboard', [

            // Data card ringkasan
            'summary' => $summary,

            // Mengambil 10 booking terbaru hari ini
            'recentBookings' => $bookings->take(10)->get(),

            // Data jumlah booking per status
            'statusCounts' => $statusCounts,

            // Total poli
            'polisCount' => Poli::count(),
        ]);
    }

    // Method untuk menampilkan halaman laporan admin
    public function reports(Request $request)
    {
        // Mengambil tanggal awal dari filter
        // Jika kosong, otomatis mulai dari awal bulan ini
        $start = $request->input(
            'start_date',
            now()->startOfMonth()->toDateString()
        );

        // Mengambil tanggal akhir dari filter
        // Jika kosong, otomatis tanggal hari ini
        $end = $request->input(
            'end_date',
            now()->toDateString()
        );

        // Mengambil data booking berdasarkan rentang tanggal
        // Sekaligus mengambil data dokter, poli, dan user
        $bookings = Booking::with('doctor.poli', 'user')
            ->whereBetween('visit_date', [$start, $end]);

        // Menghitung jumlah booking berdasarkan status
        // clone dipakai agar query utama $bookings tidak rusak/berubah
        $statusCounts = (clone $bookings)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        // Menghitung jumlah booking berdasarkan poli
        $poliCounts = (clone $bookings)

            // Menghubungkan tabel bookings dengan doctors
            ->join('doctors', 'bookings.doctor_id', '=', 'doctors.id')

            // Menghubungkan tabel doctors dengan polis
            ->join('polis', 'doctors.poli_id', '=', 'polis.id')

            // Mengambil nama poli dan jumlah booking
            ->select('polis.name', DB::raw('count(*) as total'))

            // Mengelompokkan berdasarkan nama poli
            ->groupBy('polis.name')

            // Urutkan dari jumlah booking terbanyak
            ->orderByDesc('total')

            // Ambil hasilnya
            ->get();

        // Mengirim data laporan ke view admin.reports.index
        return view('admin.reports.index', [

            // Tanggal awal laporan
            'start' => $start,

            // Tanggal akhir laporan
            'end' => $end,

            // Total booking pada rentang tanggal
            'totalBookings' => (clone $bookings)->count(),

            // Total booking yang statusnya selesai
            'completedBookings' => (clone $bookings)
                ->where('status', 'selesai')
                ->count(),

            // Total booking yang masih menunggu atau dipanggil
            'waitingBookings' => (clone $bookings)
                ->whereIn('status', ['menunggu', 'dipanggil'])
                ->count(),

            // Total booking yang dibatalkan
            'cancelledBookings' => (clone $bookings)
                ->where('status', 'dibatalkan')
                ->count(),

            // Jumlah booking berdasarkan status
            'statusCounts' => $statusCounts,

            // Jumlah booking berdasarkan poli
            'poliCounts' => $poliCounts,

            // 10 booking terbaru berdasarkan rentang tanggal
            'latestBookings' => (clone $bookings)
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }
}