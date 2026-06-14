@extends('layouts.dashboard')

@section('page-content')
<div class="max-w-6xl mx-auto" x-data="bookingForm()" x-init="init()">
    <div class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-cyan-500 to-emerald-400 p-[1px] shadow-xl shadow-blue-500/10">
        <div class="rounded-3xl bg-white/90 dark:bg-slate-950/90 px-6 py-7 sm:px-8 backdrop-blur">
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-300">Puskesmas Online</p>
            <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-slate-950 dark:text-white sm:text-4xl">Ambil Nomor Antrian</h1>
                    <p class="mt-2 max-w-2xl text-slate-600 dark:text-slate-300">Pilih poli, dokter, dan tanggal kunjungan. Sistem akan mengecek kuota lalu membuat nomor antrian beserta estimasi waktu tunggu.</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('patient.booking.store') }}" method="POST" class="grid gap-6 lg:grid-cols-[1.35fr_.65fr]">
        @csrf
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80 sm:p-8">
            <div class="mb-8 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-950 dark:text-white">Form Booking</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Data bertanda * wajib diisi.</p>
                </div>
            </div>

            <div class="space-y-7">
                <div>
                    <label class="mb-3 block text-sm font-bold text-slate-800 dark:text-slate-100"><span class="text-red-500">*</span> Pilih Poli</label>
                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($polis as $poli)
                            <label class="group relative">
                                <input type="radio" name="poli_id" value="{{ $poli->id }}" x-model="form.poli_id" @change="loadDoctors()" class="peer sr-only" required>
                                <div class="h-full cursor-pointer rounded-2xl border-2 border-slate-200 bg-slate-50/60 p-4 transition-all hover:-translate-y-0.5 hover:border-blue-300 hover:bg-white hover:shadow-md peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:border-slate-700 dark:bg-slate-800/60 dark:hover:bg-slate-800 dark:peer-checked:bg-blue-950/40">
                                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-white text-blue-600 shadow-sm dark:bg-slate-900">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m6-10h1m4 0h1m-6 4h1m4 0h1"/></svg>
                                    </div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $poli->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $poli->doctors->count() }} dokter tersedia</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-bold text-slate-800 dark:text-slate-100"><span class="text-red-500">*</span> Pilih Dokter</label>
                    <div class="grid gap-3 md:grid-cols-2">
                        <template x-for="doctor in doctors" :key="doctor.id">
                            <label class="relative">
                                <input type="radio" name="doctor_id" :value="doctor.id" x-model="form.doctor_id" @change="validateDate()" class="peer sr-only" required>
                                <div class="cursor-pointer rounded-2xl border-2 border-slate-200 bg-white p-4 transition-all hover:border-cyan-300 hover:shadow-md peer-checked:border-cyan-500 peer-checked:bg-cyan-50 dark:border-slate-700 dark:bg-slate-800 dark:peer-checked:bg-cyan-950/40">
                                    <p class="font-bold text-slate-950 dark:text-white" x-text="'Dr. ' + doctor.name"></p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400" x-text="doctor.specialty"></p>
                                    <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600 dark:bg-slate-700 dark:text-slate-300" x-text="doctor.experience_years + ' tahun pengalaman'"></span>
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200" x-text="'Kuota ' + doctor.daily_quota + '/hari'"></span>
                                    </div>
                                    <p class="mt-3 text-xs font-semibold text-cyan-700 dark:text-cyan-200" x-text="'Praktik: ' + scheduleText(doctor)"></p>
                                </div>
                            </label>
                        </template>
                    </div>
                    <div x-show="doctors.length === 0" class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-200">Pilih poli terlebih dahulu untuk menampilkan dokter.</div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-3 block text-sm font-bold text-slate-800 dark:text-slate-100"><span class="text-red-500">*</span> Pilih Tanggal</label>
                        <input type="date" name="visit_date" x-model="form.visit_date" @change="validateDate()" :min="minDate" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <p x-show="selectedDoctor()" class="mt-2 text-xs text-slate-500 dark:text-slate-400" x-text="'Jadwal dokter: ' + scheduleText(selectedDoctor())"></p>
                        <p x-show="dateError" x-text="dateError" class="mt-2 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200"></p>
                    </div>
                    <div>
                        <label class="mb-3 block text-sm font-bold text-slate-800 dark:text-slate-100">Catatan Tambahan</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Contoh: kontrol rutin / keluhan singkat" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                </div>
            </div>
        </section>

        <aside class="space-y-5">
            <div class="sticky top-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-blue-600 dark:text-blue-300">Ringkasan</p>
                <div class="mt-5 rounded-3xl bg-gradient-to-br from-blue-50 to-cyan-50 p-6 text-center ring-1 ring-blue-100 dark:from-blue-950/40 dark:to-cyan-950/30 dark:ring-blue-400/20">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Estimasi Waktu Tunggu</p>
                    <p class="mt-2 text-4xl font-black text-blue-700 dark:text-blue-200" x-text="estimatedWait()"></p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">berdasarkan antrean aktif sebelum Anda</p>
                </div>
                <div class="mt-5 space-y-3 text-sm">
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800"><span>Poli</span><strong x-text="getPoliName()"></strong></div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800"><span>Dokter</span><strong x-text="getDoctorName()"></strong></div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800"><span>Sisa Kuota</span><strong x-text="quotaText()"></strong></div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800"><span>Tanggal</span><strong x-text="form.visit_date || '-'"></strong></div>
                </div>
                <div class="mt-6 flex gap-3">
                    <a href="{{ route('patient.dashboard') }}" class="flex-1 rounded-2xl border border-slate-300 px-4 py-3 text-center font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Batal</a>
                    <button type="submit" :disabled="!canSubmit()" class="flex-1 rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-500 px-4 py-3 font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0 disabled:hover:shadow-lg">Ambil Nomor</button>
                </div>
            </div>
        </aside>
    </form>
</div>

<script>
function bookingForm() {
    return {
        form: { poli_id: '{{ old("poli_id") }}', doctor_id: '{{ old("doctor_id") }}', visit_date: '{{ old("visit_date") }}' },
        doctors: [],
        polis: @json($polis),
        minDate: new Date().toISOString().split('T')[0],
        dateError: '',
        dayNames: {
            monday: 'Senin',
            tuesday: 'Selasa',
            wednesday: 'Rabu',
            thursday: 'Kamis',
            friday: 'Jumat',
            saturday: 'Sabtu',
            sunday: 'Minggu'
        },
        loadDoctors() {
            const poli = this.polis.find(p => p.id == this.form.poli_id);
            this.doctors = poli ? poli.doctors : [];
            if (!this.doctors.some(d => d.id == this.form.doctor_id)) this.form.doctor_id = '';
            this.validateDate();
        },
        selectedDoctor() { return this.doctors.find(d => d.id == this.form.doctor_id); },
        selectedWeekday() {
            if (!this.form.visit_date) return '';
            const date = new Date(this.form.visit_date + 'T00:00:00');
            return ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'][date.getDay()];
        },
        scheduleDays(doctor) {
            return (doctor?.schedules || []).map(schedule => schedule.weekday);
        },
        formatTime(value) {
            return value ? String(value).slice(0, 5) : '';
        },
        scheduleLine(schedule) {
            const day = this.dayNames[schedule.weekday] || schedule.weekday;
            const start = this.formatTime(schedule.start_time);
            const end = this.formatTime(schedule.end_time);

            return start && end ? day + ' ' + start + '-' + end : day;
        },
        scheduleText(doctor) {
            const schedules = doctor?.schedules || [];
            return schedules.length ? schedules.map(schedule => this.scheduleLine(schedule)).join(', ') : 'belum ada jadwal';
        },
        doctorPracticesOnSelectedDate() {
            const doctor = this.selectedDoctor();
            const weekday = this.selectedWeekday();
            if (!doctor || !weekday) return true;
            return this.scheduleDays(doctor).includes(weekday);
        },
        validateDate() {
            this.dateError = '';

            const doctor = this.selectedDoctor();
            const weekday = this.selectedWeekday();

            if (!doctor || !weekday) return;

            if (!this.doctorPracticesOnSelectedDate()) {
                this.dateError = 'Dokter tidak praktik pada hari ' + (this.dayNames[weekday] || weekday) + '. Pilih tanggal lain sesuai jadwal: ' + this.scheduleText(doctor) + '.';
            }
        },
        canSubmit() {
            return this.form.poli_id && this.form.doctor_id && this.form.visit_date && !this.dateError && this.doctorPracticesOnSelectedDate();
        },
        getPoliName() { const poli = this.polis.find(p => p.id == this.form.poli_id); return poli ? poli.name : '-'; },
        getDoctorName() { const d = this.selectedDoctor(); return d ? 'Dr. ' + d.name : '-'; },
        quotaText() { const d = this.selectedDoctor(); return d ? d.daily_quota + ' pasien/hari' : '-'; },
        estimatedWait() { const d = this.selectedDoctor(); return d ? '+/- 10 menit' : '-'; },
        init() { if (this.form.poli_id) this.loadDoctors(); this.validateDate(); }
    };
}
</script>
@endsection
