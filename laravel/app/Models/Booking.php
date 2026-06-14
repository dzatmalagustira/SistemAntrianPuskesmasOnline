<?php

namespace App\Models; // Menentukan lokasi model ini berada di folder App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory; // Digunakan untuk membuat data dummy/factory
use Illuminate\Database\Eloquent\Model; // Class dasar model Laravel
use Illuminate\Notifications\Notifiable; // Agar model dapat menerima notifikasi Laravel

class Booking extends Model // Model Booking yang terhubung ke tabel bookings
{
    use HasFactory, Notifiable; // Mengaktifkan fitur Factory dan Notifikasi

    protected $fillable = [ // Kolom yang boleh diisi menggunakan create() atau update()
        'user_id', // ID pasien yang melakukan booking
        'doctor_id', // ID dokter yang dipilih
        'visit_date', // Tanggal kunjungan pasien
        'queue_number', // Nomor antrian pasien
        'status', // Status booking (menunggu, dipanggil, selesai, dibatalkan)
        'notes', // Catatan tambahan dari pasien
        'estimated_wait', // Estimasi waktu tunggu pasien
    ];

    protected $casts = [ // Mengubah tipe data otomatis saat diambil dari database
        'visit_date' => 'date', // Kolom visit_date otomatis menjadi objek tanggal (Carbon)
    ];

    public function user() // Relasi Booking ke User
    {
        return $this->belongsTo(User::class); // Satu booking dimiliki oleh satu user/pasien
    }

    public function doctor() // Relasi Booking ke Doctor
    {
        return $this->belongsTo(Doctor::class); // Satu booking memilih satu dokter
    }

    public function queue() // Relasi Booking ke QueueEntry
    {
        return $this->hasOne(QueueEntry::class); // Satu booking memiliki satu data antrian
    }
}