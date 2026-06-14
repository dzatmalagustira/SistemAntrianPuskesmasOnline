@extends('layouts.dashboard')

@section('page-content')
<div class="max-w-2xl">
    <div class="p-8 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">
            {{ $doctor->id ? 'Edit Dokter' : 'Tambah Dokter Baru' }}
        </h1>
        <p class="text-slate-600 dark:text-slate-400 mb-8">
            {{ $doctor->id ? 'Perbarui informasi dokter' : 'Tambahkan dokter baru ke sistem' }}
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

        <form action="{{ $doctor->id ? route('admin.doctors.update', $doctor) : route('admin.doctors.store') }}" method="POST" class="space-y-6">
            @csrf
            @if ($doctor->id)
                @method('PUT')
            @endif

            <div>
                <label for="poli_id" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    <span class="text-red-500">*</span> Poli
                </label>
                <select id="poli_id" name="poli_id" required
                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">-- Pilih Poli --</option>
                    @foreach ($polis as $poli)
                        <option value="{{ $poli->id }}" {{ old('poli_id', $doctor->poli_id) == $poli->id ? 'selected' : '' }}>
                            {{ $poli->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    <span class="text-red-500">*</span> Nama Dokter
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $doctor->name) }}" required
                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="Dr. Nama Dokter">
            </div>

            <div>
                <label for="specialty" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    <span class="text-red-500">*</span> Spesialisasi
                </label>
                <input type="text" id="specialty" name="specialty" value="{{ old('specialty', $doctor->specialty) }}" required
                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="Contoh: Dokter Umum">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="experience_years" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                        <span class="text-red-500">*</span> Tahun Pengalaman
                    </label>
                    <input type="number" id="experience_years" name="experience_years" value="{{ old('experience_years', $doctor->experience_years) }}" required min="0" max="50"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="5">
                </div>

                <div>
                    <label for="daily_quota" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                        <span class="text-red-500">*</span> Kuota Harian
                    </label>
                    <input type="number" id="daily_quota" name="daily_quota" value="{{ old('daily_quota', $doctor->daily_quota) }}" required min="1" max="50"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="20">
                </div>
            </div>

            <div class="flex gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('admin.doctors.index') }}" class="flex-1 px-6 py-3 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg font-semibold hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                    {{ $doctor->id ? 'Perbarui' : 'Tambah' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
