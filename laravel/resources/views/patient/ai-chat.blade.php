@extends('layouts.dashboard')

@section('page-content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Tanya AI</h1>
            <p class="text-slate-600 dark:text-slate-400">Asisten khusus untuk pertanyaan seputar aplikasi antrian online, booking, layanan puskesmas, dan edukasi kesehatan terkait.</p>
        </div>

        @if (count($messages) > 0)
            <form action="{{ route('patient.ai.reset') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100 rounded-lg font-semibold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                    Bersihkan
                </button>
            </form>
        @endif
    </div>

    <div class="rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="h-[520px] overflow-y-auto p-6 space-y-4 bg-slate-50 dark:bg-slate-900">
            @forelse ($messages as $message)
                @php
                    $displayContent = $message['content'] ?? '';
                    $displayContent = str_replace(["\\r\\n", "\\n", "\\r"], "\n", $displayContent);
                    $displayContent = str_replace(["\r\n", "\r"], "\n", $displayContent);
                    $displayContent = preg_replace([
                        '/\*\*(.*?)\*\*/s',
                        '/__(.*?)__/s',
                        '/\*(.*?)\*/s',
                        '/`{1,3}([^`]*)`{1,3}/s',
                        '/^\s{0,3}#{1,6}\s*/m',
                    ], '$1', $displayContent) ?? $displayContent;
                @endphp
                <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-3xl rounded-xl px-4 py-3 {{ $message['role'] === 'user' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-200 dark:border-slate-700' }}">
                        <div class="text-sm leading-5" style="white-space: pre-line;">{{ $displayContent }}</div>
                    </div>
                </div>
            @empty
                <div class="h-full flex items-center justify-center text-center text-slate-500 dark:text-slate-400">
                    <div>
                        <p class="font-semibold text-slate-700 dark:text-slate-200">Mulai percakapan</p>
                        <p class="text-sm mt-1">Contoh: cara membatalkan booking, dokumen saat ke puskesmas, jadwal dokter, status antrian, atau keluhan kesehatan ringan yang terkait layanan puskesmas.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <form action="{{ route('patient.ai.ask') }}" method="POST" class="p-4 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
            @csrf
            <div class="flex flex-col gap-3 md:flex-row">
                <textarea name="message" rows="2" maxlength="1000" required placeholder="Tulis pertanyaan Anda..."
                    class="flex-1 resize-none px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('message') }}</textarea>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                    Kirim
                </button>
            </div>
            <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">AI hanya menjawab topik yang berhubungan dengan web ini. Untuk keluhan berat atau darurat, segera hubungi petugas medis atau layanan gawat darurat.</p>
        </form>
    </div>
</div>
@endsection
