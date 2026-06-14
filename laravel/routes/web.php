<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\PoliController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Patient\AiChatController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'home'])->name('home');

// Route pemersatu setelah login. Jika user membuka /dashboard, otomatis diarahkan
// sesuai role sehingga tidak terjadi 404.
Route::middleware('auth')->get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('patient.dashboard');
})->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/booking/create', [PatientDashboardController::class, 'create'])->name('booking.create');
    Route::get('/booking/quota', [PatientDashboardController::class, 'quota'])
    ->name('booking.quota');
    Route::post('/booking', [PatientDashboardController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}', [PatientDashboardController::class, 'show'])->name('booking.show');
    Route::patch('/booking/{booking}/cancel', [PatientDashboardController::class, 'cancel'])->name('booking.cancel');
    Route::get('/ai', [AiChatController::class, 'index'])->name('ai.index');
    Route::post('/ai', [AiChatController::class, 'ask'])->name('ai.ask');
    Route::post('/ai/reset', [AiChatController::class, 'reset'])->name('ai.reset');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports.index');

    Route::resource('polis', PoliController::class)->except(['show']);
    Route::resource('doctors', DoctorController::class)->except(['show']);
    Route::resource('schedules', ScheduleController::class)->except(['show']);

    Route::get('/bookings/latest', [AdminBookingController::class, 'latest'])->name('bookings.latest');
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
    Route::post('/bookings/{booking}/call', [AdminBookingController::class, 'callNumber'])->name('bookings.call');
    Route::get('/bookings/export/excel', [AdminBookingController::class, 'exportExcel'])->name('bookings.export');

    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});
