@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 dark:from-slate-900 to-white dark:to-slate-950">
    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-slate-900 dark:text-white">Puskesmas Antrian</span>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="px-6 py-2 text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg font-medium hover:shadow-lg transition-all">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <div class="pt-32 pb-20 px-4">
        <div class="max-w-5xl mx-auto text-center">
            <h1 class="text-5xl md:text-7xl font-bold text-slate-900 dark:text-white mb-6">
                Antrian Online
                <span class="block bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-600 bg-clip-text text-transparent">Puskesmas Terpercaya</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-600 dark:text-slate-300 mb-10 max-w-2xl mx-auto">
                Sistem booking dan antrian online modern untuk puskesmas. Hemat waktu Anda dengan mengatur antrian dari rumah secara realtime.
            </p>

            <div class="flex gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg font-semibold hover:shadow-xl transition-all transform hover:scale-105">
                    Daftar Sekarang
                </a>
                <a href="#fitur" class="px-8 py-4 border-2 border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div id="fitur" class="py-20 px-4 bg-slate-50 dark:bg-slate-900/50">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-bold text-center text-slate-900 dark:text-white mb-16">Fitur Unggulan</h2>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-8 rounded-2xl bg-white dark:bg-slate-800 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Booking Mudah</h3>
                    <p class="text-slate-600 dark:text-slate-400">Pesan antrian dengan mudah hanya dalam beberapa klik, pilih dokter dan tanggal yang sesuai.</p>
                </div>

                <div class="p-8 rounded-2xl bg-white dark:bg-slate-800 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.82L3 20l1.23-3.69A7.64 7.64 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">AI Assistant</h3>
                    <p class="text-slate-600 dark:text-slate-400">Tanya jadwal poli, cara booking, dokumen pendaftaran, BPJS, dan keluhan ringan lewat asisten AI.</p>
                </div>

                <div class="p-8 rounded-2xl bg-white dark:bg-slate-800 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Detail Booking</h3>
                    <p class="text-slate-600 dark:text-slate-400">Lihat detail nomor antrian, dokter, poli, tanggal kunjungan, dan status booking.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="py-20 px-4">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-slate-900 dark:text-white mb-6">Siap Memulai?</h2>
            <p class="text-lg text-slate-600 dark:text-slate-300 mb-8">Daftar sekarang dan mulai memesan antrian online dengan mudah dan cepat.</p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg font-semibold hover:shadow-xl transition-all">
                Daftar Gratis
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
        <div class="max-w-6xl mx-auto px-4 py-12">
            <div class="text-center text-slate-600 dark:text-slate-400">
                <p>&copy; 2026 Puskesmas Antrian Online. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>
</div>
@endsection
