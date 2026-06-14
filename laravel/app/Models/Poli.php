<?php

namespace App\Models; // Menentukan lokasi model ini berada di folder App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory; // Digunakan untuk membuat data dummy/factory
use Illuminate\Database\Eloquent\Model; // Class dasar model Laravel

class Poli extends Model // Model Poli yang terhubung ke tabel polis
{
    use HasFactory; // Mengaktifkan fitur Factory Laravel

    protected $fillable = [ // Kolom yang boleh diisi saat create() atau update()
        'name', // Nama poli (contoh: Poli Umum, Poli Gigi)
        'description', // Deskripsi atau keterangan poli
    ];

    public function doctors() // Relasi Poli ke Doctor
    {
        return $this->hasMany(Doctor::class); // Satu poli dapat memiliki banyak dokter
    }
}