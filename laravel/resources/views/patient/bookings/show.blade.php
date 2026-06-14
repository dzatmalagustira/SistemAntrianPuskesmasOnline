@extends('layouts.dashboard')

@section('page-content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Detail Booking</h1>
            <p class="text-slate-600 dark:text-slate-400">Informasi lengkap antrian kunjungan pasien</p>
        </div>

        <a href="{{ route('patient.dashboard') }}"
           class="px-4 py-2 rounded-lg bg-slate-200 text-slate-800 text-sm font-semibold hover:bg-slate-300 transition dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600">
            Kembali
        </a>
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

    <div class="p-6 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="text-center p-6 rounded-xl bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border border-blue-200 dark:border-blue-800 mb-6">
            <p class="text-sm font-semibold text-blue-600 dark:text-blue-300">Nomor Antrian</p>
            <p class="text-5xl font-bold text-blue-700 dark:text-blue-300 mt-2">
                {{ $booking->queue_number ?? ($booking->queue->number ?? '-') }}
            </p>
            <span class="inline-block mt-4 px-4 py-2 rounded-lg text-sm font-semibold
                @if ($booking->status === 'menunggu') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200
                @elseif ($booking->status === 'dipanggil') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200
                @elseif ($booking->status === 'selesai') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200
                @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200
                @endif
            ">
                {{ ucfirst($booking->status) }}
            </span>
        </div>

        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                <p class="text-slate-600 dark:text-slate-400">Nama Pasien</p>
                <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->user->name ?? auth()->user()->name ?? '-' }}</p>
            </div>

            <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                <p class="text-slate-600 dark:text-slate-400">Tanggal Kunjungan</p>
                <p class="font-semibold text-slate-900 dark:text-white">{{ optional($booking->visit_date)->format('d M Y') ?? '-' }}</p>
            </div>

            <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                <p class="text-slate-600 dark:text-slate-400">Poli</p>
                <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->doctor->poli->name ?? '-' }}</p>
            </div>

            <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                <p class="text-slate-600 dark:text-slate-400">Dokter</p>
                <p class="font-semibold text-slate-900 dark:text-white">Dr. {{ $booking->doctor->name ?? '-' }}</p>
            </div>

            <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                <p class="text-slate-600 dark:text-slate-400">Estimasi Tunggu</p>
                <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->estimated_wait ?? '-' }}</p>
            </div>

            <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                <p class="text-slate-600 dark:text-slate-400">Catatan</p>
                <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->notes ?: '-' }}</p>
            </div>
        </div>

        @if (in_array($booking->status, ['menunggu', 'dipanggil'], true))
            <div class="mt-6 flex flex-wrap gap-2">
                <form action="{{ route('patient.booking.cancel', $booking->id) }}"
                      method="POST"
                      onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                            class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">
                        Batalkan Booking
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
