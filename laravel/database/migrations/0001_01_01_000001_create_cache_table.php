<?php

use Illuminate\Database\Migrations\Migration; // Class dasar migration Laravel
use Illuminate\Database\Schema\Blueprint; // Digunakan untuk membuat struktur tabel
use Illuminate\Support\Facades\Schema; // Digunakan untuk membuat, mengubah, dan menghapus tabel

return new class extends Migration // Membuat migration baru
{
    /**
     * Method yang dijalankan saat php artisan migrate
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) { // Membuat tabel users

            $table->id(); // Primary key auto increment

            $table->string('name'); // Nama pengguna

            $table->string('email')->unique(); // Email pengguna, harus unik

            $table->string('phone')->nullable(); // Nomor telepon, boleh kosong

            $table->text('address')->nullable(); // Alamat, boleh kosong

            $table->enum('role', ['admin', 'patient'])->default('patient');
            // Role pengguna, hanya boleh admin atau patient
            // Default otomatis patient

            $table->timestamp('email_verified_at')->nullable();
            // Waktu verifikasi email
            // Null berarti email belum diverifikasi

            $table->string('password'); // Password yang sudah di-hash

            $table->rememberToken();
            // Token untuk fitur "Remember Me" saat login

            $table->timestamps();
            // Membuat created_at dan updated_at otomatis
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // Membuat tabel password reset

            $table->string('email')->primary();
            // Email sebagai primary key

            $table->string('token');
            // Token reset password

            $table->timestamp('created_at')->nullable();
            // Waktu token dibuat
        });

        Schema::create('sessions', function (Blueprint $table) {
            // Membuat tabel sessions

            $table->string('id')->primary();
            // ID session

            $table->foreignId('user_id')->nullable()->index();
            // ID user yang sedang login
            // Nullable karena pengunjung bisa belum login

            $table->string('ip_address', 45)->nullable();
            // Menyimpan IP Address pengguna

            $table->text('user_agent')->nullable();
            // Menyimpan informasi browser pengguna

            $table->longText('payload');
            // Menyimpan data session Laravel

            $table->integer('last_activity')->index();
            // Menyimpan waktu aktivitas terakhir user
        });
    }

    /**
     * Method yang dijalankan saat php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        // Menghapus tabel users jika ada

        Schema::dropIfExists('password_reset_tokens');
        // Menghapus tabel password_reset_tokens jika ada

        Schema::dropIfExists('sessions');
        // Menghapus tabel sessions jika ada
    }
};