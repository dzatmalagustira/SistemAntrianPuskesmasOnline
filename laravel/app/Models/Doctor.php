<?php

namespace App\Models; // Menentukan lokasi model ini berada di folder App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory; // Digunakan untuk membuat data dummy/factory
use Illuminate\Database\Eloquent\Model; // Class dasar model Laravel

class Doctor extends Model // Model Doctor yang terhubung ke tabel doctors
{
    use HasFactory; // Mengaktifkan fitur Factory Laravel

    protected $fillable = [ // Kolom yang boleh diisi saat create() atau update()
        'poli_id', // ID poli tempat dokter bertugas
        'name', // Nama dokter
        'specialty', // Spesialisasi dokter
        'photo', // Foto dokter
        'experience_years', // Lama pengalaman dokter (tahun)
        'daily_quota', // Kuota pasien per hari
    ];

    public function poli() // Relasi Doctor ke Poli
    {
        return $this->belongsTo(Poli::class); // Satu dokter hanya berada pada satu poli
    }

    public function schedules() // Relasi Doctor ke Schedule
    {
        return $this->hasMany(Schedule::class); // Satu dokter bisa memiliki banyak jadwal praktik
    }

    public function bookings() // Relasi Doctor ke Booking
    {
        return $this->hasMany(Booking::class); // Satu dokter bisa memiliki banyak booking pasien
    }
}