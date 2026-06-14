<?php

// ==============================
// MEMANGGIL CONTROLLER YANG DIPERLUKAN
// ==============================

use App\Http\Controllers\Admin\BookingController as AdminBookingController; // Mengatur data booking dan antrian
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController; // Dashboard admin
use App\Http\Controllers\Admin\DoctorController; // Data dokter
use App\Http\Controllers\Admin\PatientController; // Data pasien
use App\Http\Controllers\Admin\PoliController; // Data poli
use App\Http\Controllers\Admin\ScheduleController; // Jadwal dokter

use App\Http\Controllers\AuthController; // Login, register, logout
use App\Http\Controllers\NotificationController; // Notifikasi

use App\Http\Controllers\Patient\AiChatController; // Chatbot AI pasien
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController; // Dashboard pasien

use Illuminate\Support\Facades\Route; // Digunakan untuk membuat route/URL


// ==============================
// HALAMAN AWAL WEBSITE
// ==============================

Route::get('/', [AuthController::class, 'home'])
    ->name('home');
// Saat website dibuka pertama kali
// sistem menampilkan halaman utama


// ==============================
// DASHBOARD OTOMATIS
// ==============================

Route::middleware('auth')->get('/dashboard', function () {

    return auth()->user()->isAdmin()

        ? redirect()->route('admin.dashboard')

        : redirect()->route('patient.dashboard');

});
// Setelah login,
// admin masuk ke dashboard admin,
// pasien masuk ke dashboard pasien


// ==============================
// LOGIN DAN REGISTER
// ==============================

Route::middleware('guest')->group(function () {

    Route::get('/login',
        [AuthController::class, 'showLogin']);
    // Menampilkan halaman login

    Route::post('/login',
        [AuthController::class, 'login']);
    // Memproses login user

    Route::get('/register',
        [AuthController::class, 'showRegister']);
    // Menampilkan halaman registrasi

    Route::post('/register',
        [AuthController::class, 'register']);
    // Menyimpan akun baru ke database
});


// ==============================
// LOGOUT
// ==============================

Route::post('/logout',
    [AuthController::class, 'logout']);
// Keluar dari sistem


// =================================================
// MENU KHUSUS PASIEN
// =================================================

Route::middleware(['auth', 'role:patient'])
->prefix('patient')
->group(function () {

    Route::get('/dashboard',
        [PatientDashboardController::class, 'index']);
    // Dashboard pasien

    Route::get('/booking/create',
        [PatientDashboardController::class, 'create']);
    // Form membuat booking baru

    Route::get('/booking/quota',
        [PatientDashboardController::class, 'quota']);
    // Melihat sisa kuota dokter

    Route::post('/booking',
        [PatientDashboardController::class, 'store']);
    // Menyimpan booking pasien

    Route::get('/booking/{booking}',
        [PatientDashboardController::class, 'show']);
    // Melihat detail booking

    Route::patch('/booking/{booking}/cancel',
        [PatientDashboardController::class, 'cancel']);
    // Membatalkan booking

    Route::get('/ai',
        [AiChatController::class, 'index']);
    // Membuka chatbot AI

    Route::post('/ai',
        [AiChatController::class, 'ask']);
    // Mengirim pertanyaan ke AI

    Route::post('/ai/reset',
        [AiChatController::class, 'reset']);
    // Menghapus riwayat chat AI
});


// =================================================
// MENU KHUSUS ADMIN
// =================================================

Route::middleware(['auth', 'role:admin'])
->prefix('admin')
->group(function () {

    Route::get('/dashboard',
        [AdminDashboardController::class, 'index']);
    // Dashboard admin

    Route::get('/reports',
        [AdminDashboardController::class, 'reports']);
    // Laporan sistem


    // ==========================
    // KELOLA DATA POLI
    // ==========================

    Route::resource('polis',
        PoliController::class)
        ->except(['show']);
    // Tambah, edit, hapus, dan lihat data poli


    // ==========================
    // KELOLA DATA DOKTER
    // ==========================

    Route::resource('doctors',
        DoctorController::class)
        ->except(['show']);
    // Tambah, edit, hapus, dan lihat data dokter


    // ==========================
    // KELOLA JADWAL DOKTER
    // ==========================

    Route::resource('schedules',
        ScheduleController::class)
        ->except(['show']);
    // Tambah, edit, hapus, dan lihat jadwal dokter


    // ==========================
    // KELOLA ANTRIAN
    // ==========================

    Route::get('/bookings/latest',
        [AdminBookingController::class, 'latest']);
    // Mengambil data antrian terbaru menggunakan AJAX

    Route::get('/bookings',
        [AdminBookingController::class, 'index']);
    // Menampilkan seluruh data booking

    Route::post('/bookings/{booking}/status',
        [AdminBookingController::class, 'updateStatus']);
    // Mengubah status booking

    Route::post('/bookings/{booking}/call',
        [AdminBookingController::class, 'callNumber']);
    // Memanggil nomor antrian pasien

    Route::get('/bookings/export/excel',
        [AdminBookingController::class, 'exportExcel']);
    // Export data booking ke file Excel/CSV


    // ==========================
    // KELOLA DATA PASIEN
    // ==========================

    Route::get('/patients',
        [PatientController::class, 'index']);
    // Menampilkan daftar pasien

    Route::delete('/patients/{patient}',
        [PatientController::class, 'destroy']);
    // Menghapus data pasien
});


// =================================================
// NOTIFIKASI
// =================================================

Route::middleware('auth')->group(function () {

    Route::get('/notifications',
        [NotificationController::class, 'index']);
    // Menampilkan notifikasi pengguna

    Route::post('/notifications/{id}/read',
        [NotificationController::class, 'markAsRead']);
    // Menandai notifikasi sebagai sudah dibaca
});