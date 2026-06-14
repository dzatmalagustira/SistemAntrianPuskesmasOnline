@extends('layouts.dashboard')

@section('page-content')
<div class="max-w-2xl">
    <div class="p-8 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">
            {{ $schedule->id ? 'Edit Jadwal' : 'Tambah Jadwal Baru' }}
        </h1>
        <p class="text-slate-600 dark:text-slate-400 mb-8">
            {{ $schedule->id ? 'Perbarui jadwal praktek' : 'Tambahkan jadwal praktek baru' }}
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

        <form action="{{ $schedule->id ? route('admin.schedules.update', $schedule) : route('admin.schedules.store') }}" method="POST" class="space-y-6">
            @csrf
            @if ($schedule->id)
                @method('PUT')
            @endif

            <div>
                <label for="doctor_id" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    <span class="text-red-500">*</span> Dokter
                </label>
                <select id="doctor_id" name="doctor_id" required
                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">-- Pilih Dokter --</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id', $schedule->doctor_id) == $doctor->id ? 'selected' : '' }}>
                            Dr. {{ $doctor->name }} ({{ $doctor->poli->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="weekday" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                    <span class="text-red-500">*</span> Hari
                </label>
                <select id="weekday" name="weekday" required
                    class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">-- Pilih Hari --</option>
                    <option value="monday" {{ old('weekday', $schedule->weekday) == 'monday' ? 'selected' : '' }}>Senin</option>
                    <option value="tuesday" {{ old('weekday', $schedule->weekday) == 'tuesday' ? 'selected' : '' }}>Selasa</option>
                    <option value="wednesday" {{ old('weekday', $schedule->weekday) == 'wednesday' ? 'selected' : '' }}>Rabu</option>
                    <option value="thursday" {{ old('weekday', $schedule->weekday) == 'thursday' ? 'selected' : '' }}>Kamis</option>
                    <option value="friday" {{ old('weekday', $schedule->weekday) == 'friday' ? 'selected' : '' }}>Jumat</option>
                    <option value="saturday" {{ old('weekday', $schedule->weekday) == 'saturday' ? 'selected' : '' }}>Sabtu</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                        <span class="text-red-500">*</span> Jam Mulai
                    </label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', $schedule->start_time) }}" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                        <span class="text-red-500">*</span> Jam Selesai
                    </label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time', $schedule->end_time) }}" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
            </div>

            <div class="flex gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('admin.schedules.index') }}" class="flex-1 px-6 py-3 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg font-semibold hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                    {{ $schedule->id ? 'Perbarui' : 'Tambah' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
