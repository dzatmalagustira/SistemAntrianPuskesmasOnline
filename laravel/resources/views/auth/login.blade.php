@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-cyan-50 dark:from-slate-900 dark:via-slate-950 dark:to-slate-900 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <!-- Header -->
            <div class="h-2 bg-gradient-to-r from-blue-500 to-cyan-500"></div>

            <div class="p-8">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4"></path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-3xl font-bold text-center text-slate-900 dark:text-white mb-2">Masuk Akun</h1>
                <p class="text-center text-slate-600 dark:text-slate-400 mb-8">Selamat datang di Puskesmas Antrian Online</p>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-800 dark:text-red-200">{{ $errors->first() }}</p>
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="name@example.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="••••••••">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-blue-600">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold rounded-lg hover:shadow-lg transition-all transform hover:scale-105">
                        Masuk
                    </button>
                </form>

                <!-- Divider -->
                <div class="mt-6 text-center">
                    <p class="text-slate-600 dark:text-slate-400">Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">Daftar di sini</a></p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-slate-500 dark:text-slate-400 mt-6">&copy; 2026 Puskesmas Antrian Online</p>
    </div>
</div>
@endsection
