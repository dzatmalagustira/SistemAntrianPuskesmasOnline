@extends('layouts.dashboard')

@section('page-content')
<div class="space-y-8">
    <div class="rounded-3xl bg-gradient-to-r from-blue-600 via-cyan-500 to-emerald-400 p-[1px] shadow-xl shadow-blue-500/10">
        <div class="rounded-3xl bg-white/95 p-6 dark:bg-slate-950/90 sm:p-8">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-300">Laporan Statistik</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 dark:text-white">Monitoring Antrian Puskesmas</h1>
                    <p class="mt-2 max-w-2xl text-slate-600 dark:text-slate-300">Ringkasan jumlah pasien, status antrian, dan distribusi kunjungan per poli untuk mendukung evaluasi pelayanan.</p>
                </div>
                <form class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]" method="GET">
                    <input type="date" name="start_date" value="{{ $start }}" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                    <input type="date" name="end_date" value="{{ $end }}" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                    <button class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5">Filter</button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Total Booking', 'value' => $totalBookings, 'hint' => 'Semua reservasi'],
            ['label' => 'Menunggu/Dipanggil', 'value' => $waitingBookings, 'hint' => 'Antrian aktif'],
            ['label' => 'Selesai', 'value' => $completedBookings, 'hint' => 'Pelayanan tuntas'],
            ['label' => 'Dibatalkan', 'value' => $cancelledBookings, 'hint' => 'Reservasi batal'],
        ] as $card)
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p>
                <p class="mt-3 text-4xl font-black text-slate-950 dark:text-white">{{ $card['value'] }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $card['hint'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
            <h2 class="text-lg font-bold text-slate-950 dark:text-white">Status Antrian</h2>
            <div class="mt-5 space-y-4">
                @forelse ($statusCounts as $status => $count)
                    @php $percent = $totalBookings > 0 ? round(($count / $totalBookings) * 100) : 0; @endphp
                    <div>
                        <div class="mb-2 flex justify-between text-sm"><span class="font-semibold capitalize text-slate-700 dark:text-slate-200">{{ $status }}</span><span class="text-slate-500">{{ $count }} ({{ $percent }}%)</span></div>
                        <div class="h-3 rounded-full bg-slate-100 dark:bg-slate-800"><div class="h-3 rounded-full bg-gradient-to-r from-blue-600 to-cyan-400" style="width: {{ $percent }}%"></div></div>
                    </div>
                @empty
                    <p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500 dark:bg-slate-800 dark:text-slate-400">Belum ada data pada rentang tanggal ini.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
            <h2 class="text-lg font-bold text-slate-950 dark:text-white">Kunjungan per Poli</h2>
            <div class="mt-5 space-y-4">
                @forelse ($poliCounts as $poli)
                    @php $percent = $totalBookings > 0 ? round(($poli->total / $totalBookings) * 100) : 0; @endphp
                    <div>
                        <div class="mb-2 flex justify-between text-sm"><span class="font-semibold text-slate-700 dark:text-slate-200">{{ $poli->name }}</span><span class="text-slate-500">{{ $poli->total }} pasien</span></div>
                        <div class="h-3 rounded-full bg-slate-100 dark:bg-slate-800"><div class="h-3 rounded-full bg-gradient-to-r from-emerald-500 to-cyan-400" style="width: {{ $percent }}%"></div></div>
                    </div>
                @empty
                    <p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500 dark:bg-slate-800 dark:text-slate-400">Belum ada data poli pada rentang tanggal ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5 dark:border-slate-800">
            <h2 class="text-lg font-bold text-slate-950 dark:text-white">Booking Terbaru</h2>
            <a href="{{ route('admin.bookings.export') }}" class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-bold text-white dark:bg-white dark:text-slate-950">Export CSV</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/60 dark:text-slate-300">
                    <tr><th class="px-6 py-3">Nomor</th><th class="px-6 py-3">Pasien</th><th class="px-6 py-3">Poli</th><th class="px-6 py-3">Dokter</th><th class="px-6 py-3">Tanggal</th><th class="px-6 py-3">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($latestBookings as $booking)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-6 py-4 font-black text-blue-600 dark:text-blue-300">{{ $booking->queue_number }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-100">{{ $booking->user->name }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $booking->doctor->poli->name }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">Dr. {{ $booking->doctor->name }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $booking->visit_date->format('d M Y') }}</td>
                            <td class="px-6 py-4"><span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold capitalize text-blue-700 dark:bg-blue-950/50 dark:text-blue-200">{{ $booking->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada booking.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
