@extends('layouts.dashboard')

@section('page-content')
<div class="space-y-6" data-bookings-poll-url="{{ route('admin.bookings.latest', request()->query()) }}">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Kelola Antrian</h1>
            <p class="text-slate-600 dark:text-slate-400">Kelola status dan urutan antrian pasien</p>
        </div>
        <a href="{{ route('admin.bookings.index', request()->query()) }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100 rounded-lg font-semibold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
            Refresh
        </a>
    </div>

    <!-- Filter -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
            <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Cari Nomor Antrian</label>
            <input type="text" form="filter-form" name="search" value="{{ request('search') }}" placeholder="A001..."
                class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
            <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Status</label>
            <select form="filter-form" name="status"
                class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
            <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Tanggal</label>
            <input type="date" form="filter-form" name="visit_date" value="{{ request('visit_date') }}"
                class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 flex items-end gap-2">
            <form id="filter-form" action="{{ route('admin.bookings.index') }}" method="GET" class="w-full flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition-colors">
                    Filter
                </button>
                <a href="{{ route('admin.bookings.export') }}" class="px-4 py-2 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors">
                    Export
                </a>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
                    <tr class="text-slate-600 dark:text-slate-400 text-left">
                        <th class="px-6 py-4 font-semibold">No. Antrian</th>
                        <th class="px-6 py-4 font-semibold">Pasien</th>
                        <th class="px-6 py-4 font-semibold">Dokter</th>
                        <th class="px-6 py-4 font-semibold">Poli</th>
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold">Catatan</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody id="booking-table-body" class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse ($bookings as $booking)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $booking->queue_number }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->user->name }}</p>
                                <p class="text-xs text-slate-600 dark:text-slate-400">{{ $booking->user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                Dr. {{ $booking->doctor->name }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $booking->doctor->poli->name }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $booking->visit_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 max-w-xs">
                                {{ $booking->notes ?: '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.bookings.status', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()"
                                        class="px-3 py-1 rounded-lg text-xs font-semibold border-0 cursor-pointer
                                            @if ($booking->status === 'menunggu') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200
                                            @elseif ($booking->status === 'dipanggil') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200
                                            @elseif ($booking->status === 'selesai') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200
                                            @else bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200
                                            @endif
                                        ">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" {{ $booking->status === $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <form action="{{ route('admin.bookings.call', $booking) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 text-sm bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                                            Panggil
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <p class="font-medium">Tidak ada antrian ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($bookings->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $bookings->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script>
        const bookingTableBody = document.getElementById('booking-table-body');
        const pollContainer = document.querySelector('[data-bookings-poll-url]');
        const bookingsPollUrl = pollContainer ? pollContainer.dataset.bookingsPollUrl : null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const statuses = @json($statuses);

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function statusClasses(status) {
            if (status === 'menunggu') {
                return 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200';
            }

            if (status === 'dipanggil') {
                return 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200';
            }

            if (status === 'selesai') {
                return 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200';
            }

            return 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200';
        }

        function renderStatusOptions(currentStatus) {
            return statuses.map(function (status) {
                const selected = status === currentStatus ? 'selected' : '';
                return `<option value="${escapeHtml(status)}" ${selected}>${escapeHtml(status.charAt(0).toUpperCase() + status.slice(1))}</option>`;
            }).join('');
        }

        function renderBookingRow(booking) {
            return `
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400">${escapeHtml(booking.queue_number)}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-slate-900 dark:text-white">${escapeHtml(booking.patient_name)}</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400">${escapeHtml(booking.patient_email)}</p>
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                        Dr. ${escapeHtml(booking.doctor_name)}
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                        ${escapeHtml(booking.poli_name)}
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                        ${escapeHtml(booking.visit_date)}
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 max-w-xs">
   						 ${escapeHtml(booking.notes || '-')}
					</td>
                    <td class="px-6 py-4">
                        <form action="${escapeHtml(booking.status_url)}" method="POST" class="inline">
                            <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                            <select name="status" onchange="this.form.submit()"
                                class="px-3 py-1 rounded-lg text-xs font-semibold border-0 cursor-pointer ${statusClasses(booking.status)}">
                                ${renderStatusOptions(booking.status)}
                            </select>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <form action="${escapeHtml(booking.call_url)}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                <button type="submit" class="px-3 py-1 text-sm bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                                    Panggil
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            `;
        }

        async function refreshBookings() {
            if (!bookingsPollUrl || !bookingTableBody || document.querySelector('select:focus, input:focus')) {
                return;
            }

            try {
                const response = await fetch(bookingsPollUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();

                if (!Array.isArray(data.bookings)) {
                    return;
                }

                bookingTableBody.innerHTML = data.bookings.length
                    ? data.bookings.map(renderBookingRow).join('')
                    : `<tr><td colspan="8" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400"><p class="font-medium">Tidak ada antrian ditemukan</p></td></tr>`;
            } catch (error) {
                console.error('Gagal memperbarui data antrian.', error);
            }
        }

        window.setInterval(refreshBookings, 5000);
    </script>
@endpush
