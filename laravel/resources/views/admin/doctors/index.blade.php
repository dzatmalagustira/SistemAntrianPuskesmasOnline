@extends('layouts.dashboard')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Kelola Dokter</h1>
            <p class="text-slate-600 dark:text-slate-400">Kelola semua dokter di puskesmas</p>
        </div>
        <a href="{{ route('admin.doctors.create') }}" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg font-semibold hover:shadow-lg transition-all">
            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Dokter
        </a>
    </div>

    <!-- Table -->
    <div class="rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
                    <tr class="text-slate-600 dark:text-slate-400 text-left">
                        <th class="px-6 py-4 font-semibold">Nama Dokter</th>
                        <th class="px-6 py-4 font-semibold">Poli</th>
                        <th class="px-6 py-4 font-semibold">Spesialisasi</th>
                        <th class="px-6 py-4 font-semibold">Pengalaman</th>
                        <th class="px-6 py-4 font-semibold">Kuota Harian</th>
                        <th class="px-6 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse ($doctors as $doctor)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900 dark:text-white">Dr. {{ $doctor->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $doctor->poli->name }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $doctor->specialty }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-full text-xs font-semibold">
                                    {{ $doctor->experience_years }} tahun
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">
                                    {{ $doctor->daily_quota }} pasien
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.doctors.edit', $doctor) }}" class="px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="px-3 py-1 text-sm bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <p class="font-medium">Belum ada dokter</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($doctors->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $doctors->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
