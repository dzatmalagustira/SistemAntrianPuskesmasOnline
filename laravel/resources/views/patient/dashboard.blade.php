@extends('layouts.dashboard')

@section('page-content')
<div class="space-y-6">
    @if ($calledNotification)
        @php
            $calledData = $calledNotification->data ?? [];
            $calledMessage = $calledData['message'] ?? 'Nomor antrian Anda sedang dipanggil.';
            $calledUrl = $calledData['url'] ?? route('notifications.index');
        @endphp

        <div id="called-notification-popup" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/60 px-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-2xl dark:bg-slate-900">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>

                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Antrian Dipanggil</h2>
                <p class="mt-2 text-slate-600 dark:text-slate-300">{{ $calledMessage }}</p>

                <div class="mt-6 flex gap-3">
                    <a href="{{ $calledUrl }}" class="flex-1 rounded-lg bg-blue-600 px-4 py-3 text-center font-semibold text-white hover:bg-blue-700">
                        Lihat Detail
                    </a>
                    <button type="button" onclick="document.getElementById('called-notification-popup').remove()" class="flex-1 rounded-lg border border-slate-300 px-4 py-3 font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Dashboard Pasien</h1>
            <p class="text-slate-600 dark:text-slate-400">Kelola booking antrian Anda dengan mudah</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('patient.dashboard') }}" class="px-4 py-3 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100 rounded-lg font-semibold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                Refresh
            </a>
            <a href="{{ route('patient.booking.create') }}" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Booking Baru
            </a>
        </div>
    </div>


    @if (session('success'))
        <div class="p-4 rounded-lg bg-green-100 text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-lg bg-red-100 text-red-800 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Antrian Aktif</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $summary['active_bookings'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Riwayat Booking</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $summary['total_history'] }}</p>
                </div>
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Kunjungan Berikutnya</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white mt-2">{{ $summary['next_visit'] ?? '-' }}</p>
                </div>
                <div class="p-3 rounded-lg bg-orange-100 dark:bg-orange-900/30">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Upcoming Bookings -->
        <div class="lg:col-span-2 p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Antrian Aktif</h2>

            <div class="space-y-4">
                @forelse ($upcoming as $booking)
                    <div class="p-5 rounded-lg bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $booking->queue_number }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Nomor Antrian Anda</p>
                            </div>
                            <span class="px-4 py-2 rounded-lg text-sm font-semibold
                                @if ($booking->status === 'menunggu') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200
                                @elseif ($booking->status === 'dipanggil') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200
                                @else bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200
                                @endif
                            ">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                            <div>
                                <p class="text-slate-600 dark:text-slate-400">Dokter</p>
                                <p class="font-semibold text-slate-900 dark:text-white">Dr. {{ $booking->doctor->name }}</p>
                            </div>
                            <div>
                                <p class="text-slate-600 dark:text-slate-400">Poli</p>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->doctor->poli->name }}</p>
                            </div>
                            <div>
                                <p class="text-slate-600 dark:text-slate-400">Tanggal Kunjungan</p>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->visit_date->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-slate-600 dark:text-slate-400">Estimasi Tunggu</p>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->estimated_wait }}</p>
                            </div>
                        </div>

                        @if ($booking->notes)
                            <div class="p-3 rounded-lg bg-slate-100 dark:bg-slate-700/50 mb-4">
                                <p class="text-sm text-slate-600 dark:text-slate-400">Catatan: {{ $booking->notes }}</p>
                            </div>
                        @endif

                     <div class="grid grid-cols-2 gap-3">

    <a href="{{ route('patient.booking.show', $booking->id) }}"
       style="display:flex;align-items:center;justify-content:center;
              padding:12px;border-radius:8px;
              background:#2563eb;color:white;
              font-weight:600;text-decoration:none;">
        Lihat Detail
    </a>

    <form action="{{ route('patient.booking.cancel', $booking->id) }}"
          method="POST"
          style="margin:0;"
          onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
        @csrf
        @method('PATCH')

        <button type="submit"
                style="width:100%;
                       padding:12px;
                       border:none;
                       border-radius:8px;
                       background:#dc2626;
                       color:white;
                       font-weight:600;
                       cursor:pointer;">
            Batalkan Booking
        </button>
    </form>

</div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-500 dark:text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 8a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="font-medium">Tidak ada antrian aktif</p>
                        <p class="text-sm">Buat booking baru untuk memulai</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Info -->
        <div class="space-y-6">
            <!-- Booking Quick Create -->
            <div class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-slate-900 dark:text-white mb-4">Buat Booking Baru</h3>
                <div class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                    <p>- Pilih poli dan dokter</p>
                    <p>- Tentukan tanggal kunjungan</p>
                    <p>- Nomor antrian otomatis</p>
                    <p>- Cek status dari dashboard</p>
                </div>
                <a href="{{ route('patient.booking.create') }}" class="mt-4 block w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white text-center rounded-lg font-semibold hover:shadow-lg transition-all">
                    Booking Sekarang
                </a>
            </div>

            <!-- Info Box -->
            <div class="p-6 rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800">
                <h3 class="font-bold text-green-900 dark:text-green-100 mb-3">Tips</h3>
                <ul class="space-y-2 text-sm text-green-800 dark:text-green-200">
                    <li>- Datang 10 menit sebelum jadwal</li>
                    <li>- Bawa kartu identitas asli</li>
                    <li>- Gunakan tombol Batalkan Booking jika tidak jadi datang</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- History -->
    @if ($history->count() > 0)
        <div class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Riwayat Booking</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-200 dark:border-slate-700">
                        <tr class="text-slate-600 dark:text-slate-400 text-left">
                            <th class="pb-3 font-semibold">Nomor Antrian</th>
                            <th class="pb-3 font-semibold">Dokter</th>
                            <th class="pb-3 font-semibold">Poli</th>
                            <th class="pb-3 font-semibold">Tanggal</th>
                            <th class="pb-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach ($history as $booking)
                            <tr class="text-slate-900 dark:text-white">
                                <td class="py-4 font-medium">{{ $booking->queue_number }}</td>
                                <td class="py-4">Dr. {{ $booking->doctor->name }}</td>
                                <td class="py-4">{{ $booking->doctor->poli->name }}</td>
                                <td class="py-4">{{ $booking->visit_date->format('d M Y') }}</td>
                                <td class="py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if ($booking->status === 'selesai') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200
                                        @else bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200
                                        @endif
                                    ">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
    <script>
        window.setTimeout(function () {
            window.location.reload();
        }, 30000);
    </script>
@endpush
