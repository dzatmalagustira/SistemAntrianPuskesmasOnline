@extends('layouts.dashboard')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Dashboard Admin</h1>
            <p class="text-slate-600 dark:text-slate-400">Selamat datang kembali, {{ auth()->user()->name }}</p>
        </div>
        <div class="text-sm text-slate-600 dark:text-slate-400">
            {{ now()->format('l, d F Y') }}
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Pasien</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $summary['total_pasien'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 0a1 1 0 11-2 0 1 1 0 012 0zM5 20a6 6 0 1112 0v2H5v-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Booking</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $summary['total_booking'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-cyan-100 dark:bg-cyan-900/30">
                    <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Dokter</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $summary['total_dokter'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Antrian Hari Ini</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $summary['today_queue'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-orange-100 dark:bg-orange-900/30">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Recent Bookings -->
        <div class="lg:col-span-2 p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Antrian Terbaru Hari Ini</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Lihat Semua</a>
            </div>

            <div class="space-y-3">
                @forelse ($recentBookings as $booking)
                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->queue_number }}</p>
                                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $booking->user->name }} - Dr. {{ $booking->doctor->name }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium
                                    @if ($booking->status === 'menunggu') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200
                                    @elseif ($booking->status === 'dipanggil') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200
                                    @elseif ($booking->status === 'selesai') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200
                                    @else bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200
                                    @endif
                                ">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500 dark:text-slate-400">
                        <p>Tidak ada antrian hari ini</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Status Summary -->
        <div class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Ringkasan Status</h2>

            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Menunggu</span>
                        <span class="text-lg font-bold text-slate-900 dark:text-white">{{ $statusCounts['menunggu'] ?? 0 }}</span>
                    </div>
                    <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-500" style="width: {{ ($statusCounts['menunggu'] ?? 0) / max(1, array_sum($statusCounts)) * 100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Dipanggil</span>
                        <span class="text-lg font-bold text-slate-900 dark:text-white">{{ $statusCounts['dipanggil'] ?? 0 }}</span>
                    </div>
                    <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500" style="width: {{ ($statusCounts['dipanggil'] ?? 0) / max(1, array_sum($statusCounts)) * 100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Selesai</span>
                        <span class="text-lg font-bold text-slate-900 dark:text-white">{{ $statusCounts['selesai'] ?? 0 }}</span>
                    </div>
                    <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500" style="width: {{ ($statusCounts['selesai'] ?? 0) / max(1, array_sum($statusCounts)) * 100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Dibatalkan</span>
                        <span class="text-lg font-bold text-slate-900 dark:text-white">{{ $statusCounts['dibatalkan'] ?? 0 }}</span>
                    </div>
                    <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-red-500" style="width: {{ ($statusCounts['dibatalkan'] ?? 0) / max(1, array_sum($statusCounts)) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('admin.polis.create') }}" class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-lg transition-all group">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-blue-600">Tambah Poli</h3>
                <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400">Total: {{ $polisCount }}</p>
        </a>

        <a href="{{ route('admin.doctors.create') }}" class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-lg transition-all group">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-blue-600">Tambah Dokter</h3>
                <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400">Total: {{ $summary['total_dokter'] }}</p>
        </a>

        <a href="{{ route('admin.schedules.create') }}" class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-lg transition-all group">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-blue-600">Jadwal Dokter</h3>
                <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400">Kelola jadwal dokter</p>
        </a>

        <a href="{{ route('admin.bookings.export') }}" class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-lg transition-all group">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-blue-600">Export Data</h3>
                <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400">Export booking ke CSV</p>
        </a>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.setTimeout(function () {
            window.location.reload();
        }, 30000);
    </script>
@endpush
