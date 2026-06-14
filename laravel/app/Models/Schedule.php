<?php

namespace App\Models; // Menentukan lokasi model ini berada di folder App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory; // Digunakan untuk membuat data dummy/factory
use Illuminate\Database\Eloquent\Model; // Class dasar model Laravel

class Schedule extends Model // Model Schedule yang terhubung ke tabel schedules
{
    use HasFactory; // Mengaktifkan fitur Factory Laravel

    protected $fillable = [ // Kolom yang boleh diisi saat create() atau update()
        'doctor_id', // ID dokter yang memiliki jadwal praktik
        'weekday', // Hari praktik dokter (Senin, Selasa, dll)
        'start_time', // Jam mulai praktik
        'end_time', // Jam selesai praktik
    ];

    public function doctor() // Relasi Schedule ke Doctor
    {
        return $this->belongsTo(Doctor::class); // Satu jadwal dimiliki oleh satu dokter
    }
}