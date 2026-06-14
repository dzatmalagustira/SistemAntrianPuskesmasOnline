@extends('layouts.dashboard')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Kelola Poli</h1>
            <p class="text-slate-600 dark:text-slate-400">Kelola semua poli di puskesmas</p>
        </div>
        <a href="{{ route('admin.polis.create') }}" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg font-semibold hover:shadow-lg transition-all">
            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Poli
        </a>
    </div>

    <!-- Table -->
    <div class="rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
                    <tr class="text-slate-600 dark:text-slate-400 text-left">
                        <th class="px-6 py-4 font-semibold">Nama Poli</th>
                        <th class="px-6 py-4 font-semibold">Deskripsi</th>
                        <th class="px-6 py-4 font-semibold">Jumlah Dokter</th>
                        <th class="px-6 py-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse ($polis as $poli)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $poli->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ Str::limit($poli->description, 50) ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-full text-xs font-semibold">
                                    {{ $poli->doctors->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.polis.edit', $poli) }}" class="px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.polis.destroy', $poli) }}" method="POST" class="inline">
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
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <p class="font-medium">Belum ada poli</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($polis->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $polis->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
