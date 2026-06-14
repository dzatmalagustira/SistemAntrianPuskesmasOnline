@extends('layouts.dashboard')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Kelola Pasien</h1>
            <p class="text-slate-600 dark:text-slate-400">Kelola semua pasien terdaftar</p>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="p-4 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
        <form action="{{ route('admin.patients.index') }}" method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pasien..."
                class="flex-1 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition-colors">
                Cari
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
                    <tr class="text-slate-600 dark:text-slate-400 text-left">
                        <th class="px-6 py-4 font-semibold">Nama</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold">No. Telepon</th>
                        <th class="px-6 py-4 font-semibold">Terdaftar</th>
                        <th class="px-6 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse ($patients as $patient)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $patient->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $patient->email }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $patient->phone ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $patient->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.patients.destroy', $patient) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="px-3 py-1 text-sm bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <p class="font-medium">Belum ada pasien terdaftar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($patients->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $patients->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
