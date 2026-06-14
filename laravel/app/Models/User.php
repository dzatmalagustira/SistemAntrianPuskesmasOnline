<?php

namespace App\Models; // Menentukan lokasi model ini berada di folder App\Models

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Digunakan jika ingin fitur verifikasi email (saat ini tidak dipakai)

use Database\Factories\UserFactory; // Factory untuk membuat data dummy user
use Illuminate\Database\Eloquent\Factories\HasFactory; // Mengaktifkan fitur Factory Laravel
use Illuminate\Foundation\Auth\User as Authenticatable; // Class User khusus Laravel untuk login/authentication
use Illuminate\Notifications\Notifiable; // Agar user bisa menerima notifikasi
use Laravel\Sanctum\HasApiTokens; // Digunakan jika membuat API Token dengan Laravel Sanctum

class User extends Authenticatable // Model User yang terhubung ke tabel users
{
    use HasApiTokens; // Mengaktifkan fitur API Token

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable; // Mengaktifkan Factory dan Notifikasi

    /**
     * Kolom yang boleh diisi menggunakan create() atau update()
     */
    protected $fillable = [
        'name', // Nama user
        'email', // Email user
        'password', // Password user
        'role', // Role user (admin atau patient)
        'phone', // Nomor telepon user
        'address', // Alamat user
    ];

    /**
     * Kolom yang tidak akan ditampilkan saat data user diubah menjadi JSON/Array
     */
    protected $hidden = [
        'password', // Menyembunyikan password
        'remember_token', // Menyembunyikan token remember me
    ];

    /**
     * Mengubah tipe data otomatis
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Mengubah kolom email_verified_at menjadi tipe tanggal/waktu
            'password' => 'hashed', // Password otomatis di-hash/enkripsi sebelum disimpan
        ];
    }

    public function bookings() // Relasi User ke Booking
    {
        return $this->hasMany(Booking::class); // Satu user dapat memiliki banyak booking
    }

    public function activityLogs() // Relasi User ke ActivityLog
    {
        return $this->hasMany(ActivityLog::class); // Satu user dapat memiliki banyak riwayat aktivitas
    }

    public function isAdmin(): bool // Mengecek apakah user adalah admin
    {
        return $this->role === 'admin'; // Mengembalikan true jika role admin
    }

    public function isPatient(): bool // Mengecek apakah user adalah pasien
    {
        return $this->role === 'patient'; // Mengembalikan true jika role patient
    }
}