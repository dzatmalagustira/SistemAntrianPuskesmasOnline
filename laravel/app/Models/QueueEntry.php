<?php

namespace App\Models; // Menentukan lokasi model ini berada di folder App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory; // Digunakan untuk membuat data dummy/factory
use Illuminate\Database\Eloquent\Model; // Class dasar model Laravel

class QueueEntry extends Model // Model QueueEntry yang terhubung ke tabel queues
{
    use HasFactory; // Mengaktifkan fitur Factory Laravel

    protected $table = 'queues'; // Nama tabel di database adalah queues

    protected $fillable = [ // Kolom yang boleh diisi saat create() atau update()
        'booking_id', // ID booking yang terkait dengan antrian
        'doctor_id', // ID dokter yang dipilih pasien
        'user_id', // ID pasien yang mengambil antrian
        'date', // Tanggal antrian
        'number', // Nomor antrian
        'status', // Status antrian (menunggu, dipanggil, selesai, dibatalkan)
    ];

    protected $casts = [ // Mengubah tipe data otomatis saat diambil dari database
        'date' => 'date', // Kolom date otomatis menjadi objek tanggal (Carbon)
    ];

    public function booking() // Relasi QueueEntry ke Booking
    {
        return $this->belongsTo(Booking::class); // Satu antrian berasal dari satu booking
    }

    public function doctor() // Relasi QueueEntry ke Doctor
    {
        return $this->belongsTo(Doctor::class); // Satu antrian terkait dengan satu dokter
    }

    public function user() // Relasi QueueEntry ke User
    {
        return $this->belongsTo(User::class); // Satu antrian dimiliki oleh satu pasien
    }
}