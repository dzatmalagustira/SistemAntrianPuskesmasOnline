@extends('layouts.dashboard')

@section('page-content')
<div class="max-w-2xl">
    <div class="p-8 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">
            {{ $poli->id ? 'Edit Poli' : 'Tambah Poli Baru' }}
        </h1>
        <p class="text-slate-600 dark:text-slate-400 mb-8">
            {{ $poli->id ? 'Perbarui informasi poli' : 'Tambahkan poli baru ke sistem' }}
        </p>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                <ul class="text-sm text-red-800 dark:text-red-200 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $poli->id ? route('admin.polis.update', $poli) : route('admin.polis.store') }}" method="POST" class="space-y-6">
            @csrf
            @if ($poli->id)
                @method('PUT')
            @endif

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    <span class="text-red-500">*</span> Nama Poli
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $poli->name) }}" required
                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="Contoh: Poli Umum">
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    Deskripsi
                </label>
                <textarea id="description" name="description" rows="4"
                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="Deskripsi poli...">{{ old('description', $poli->description) }}</textarea>
            </div>

            <div class="flex gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('admin.polis.index') }}" class="flex-1 px-6 py-3 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg font-semibold hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                    {{ $poli->id ? 'Perbarui' : 'Tambah' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
