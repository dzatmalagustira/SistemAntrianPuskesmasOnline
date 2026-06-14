<?php

use Illuminate\Database\Migrations\Migration; // Class dasar migration Laravel
use Illuminate\Database\Schema\Blueprint; // Digunakan untuk mengubah struktur tabel
use Illuminate\Support\Facades\Schema; // Digunakan untuk membuat, mengubah, dan menghapus tabel

return new class extends Migration // Membuat migration baru
{
    public function up(): void // Dijalankan saat php artisan migrate
    {
        Schema::table('users', function (Blueprint $table) { // Mengubah tabel users yang sudah ada

            if (! Schema::hasColumn('users', 'phone')) { // Jika kolom phone belum ada

                $table->string('phone', 25)
                    ->nullable()
                    ->after('password');
                // Menambahkan kolom phone
                // Maksimal 25 karakter
                // Boleh kosong
                // Diletakkan setelah kolom password
            }

            if (! Schema::hasColumn('users', 'address')) { // Jika kolom address belum ada

                $table->text('address')
                    ->nullable()
                    ->after('phone');
                // Menambahkan kolom address
                // Tipe text untuk alamat panjang
                // Boleh kosong
                // Diletakkan setelah kolom phone
            }

            if (! Schema::hasColumn('users', 'role')) { // Jika kolom role belum ada

                $table->enum('role', ['admin', 'patient'])
                    ->default('patient')
                    ->after('address');
                // Menambahkan kolom role
                // Nilai hanya boleh admin atau patient
                // Default otomatis patient
                // Diletakkan setelah kolom address
            }
        });
    }

    public function down(): void // Dijalankan saat php artisan migrate:rollback
    {
        Schema::table('users', function (Blueprint $table) { // Mengubah tabel users

            if (Schema::hasColumn('users', 'address')) { // Jika kolom address ada

                $table->dropColumn('address');
                // Menghapus kolom address
            }

            if (Schema::hasColumn('users', 'phone')) { // Jika kolom phone ada

                $table->dropColumn('phone');
                // Menghapus kolom phone
            }

            if (Schema::hasColumn('users', 'role')) { // Jika kolom role ada

                $table->dropColumn('role');
                // Menghapus kolom role
            }
        });
    }
};