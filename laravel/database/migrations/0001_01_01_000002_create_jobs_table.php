<?php

use Illuminate\Database\Migrations\Migration; // Class dasar migration Laravel
use Illuminate\Database\Schema\Blueprint; // Digunakan untuk membuat struktur tabel
use Illuminate\Support\Facades\Schema; // Digunakan untuk membuat dan menghapus tabel

return new class extends Migration // Membuat migration baru
{
    /**
     * Method yang dijalankan saat php artisan migrate
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) { // Membuat tabel jobs

            $table->id(); // Primary key auto increment

            $table->string('queue')->index();
            // Nama antrian (queue)
            // Index digunakan agar pencarian lebih cepat

            $table->longText('payload');
            // Menyimpan data pekerjaan (job) dalam format JSON

            $table->unsignedTinyInteger('attempts');
            // Menyimpan jumlah percobaan menjalankan job

            $table->unsignedInteger('reserved_at')->nullable();
            // Waktu saat job mulai diproses
            // Bisa kosong jika belum diproses

            $table->unsignedInteger('available_at');
            // Waktu kapan job siap dijalankan

            $table->unsignedInteger('created_at');
            // Waktu job dibuat
        });

        Schema::create('job_batches', function (Blueprint $table) { // Membuat tabel job_batches

            $table->string('id')->primary();
            // ID batch job

            $table->string('name');
            // Nama batch job

            $table->integer('total_jobs');
            // Total jumlah job dalam batch

            $table->integer('pending_jobs');
            // Jumlah job yang masih menunggu

            $table->integer('failed_jobs');
            // Jumlah job yang gagal

            $table->longText('failed_job_ids');
            // Menyimpan daftar ID job yang gagal

            $table->mediumText('options')->nullable();
            // Menyimpan konfigurasi tambahan batch

            $table->integer('cancelled_at')->nullable();
            // Waktu batch dibatalkan

            $table->integer('created_at');
            // Waktu batch dibuat

            $table->integer('finished_at')->nullable();
            // Waktu batch selesai
        });

        Schema::create('failed_jobs', function (Blueprint $table) { // Membuat tabel failed_jobs

            $table->id(); // Primary key

            $table->string('uuid')->unique();
            // UUID unik untuk job yang gagal

            $table->text('connection');
            // Nama koneksi queue yang digunakan

            $table->text('queue');
            // Nama queue tempat job dijalankan

            $table->longText('payload');
            // Data job yang gagal

            $table->longText('exception');
            // Pesan error atau exception yang menyebabkan job gagal

            $table->timestamp('failed_at')->useCurrent();
            // Waktu saat job gagal dijalankan
        });
    }

    /**
     * Method yang dijalankan saat php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        // Menghapus tabel jobs

        Schema::dropIfExists('job_batches');
        // Menghapus tabel job_batches

        Schema::dropIfExists('failed_jobs');
        // Menghapus tabel failed_jobs
    }
};