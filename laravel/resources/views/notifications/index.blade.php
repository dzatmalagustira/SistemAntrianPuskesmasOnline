@extends('layouts.dashboard')

@section('page-content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Notifikasi</h1>
            <p class="text-slate-600 dark:text-slate-400">Daftar pemberitahuan status booking dan antrian pasien.</p>
        </div>
    </div>

    <div class="rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        @forelse ($notifications as $notification)
            @php
                $data = $notification->data ?? [];
                $title = $data['title'] ?? 'Notifikasi Booking';
                $message = $data['message'] ?? ($data['status'] ?? 'Ada pembaruan pada akun Anda.');
            @endphp

            <div class="p-5 border-b border-slate-200 dark:border-slate-700 {{ is_null($notification->read_at) ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white">{{ $title }}</p>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $message }}</p>
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-500">{{ $notification->created_at->format('d M Y H:i') }}</p>
                    </div>

                    @if (is_null($notification->read_at))
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-lg bg-blue-500 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-600">
                                Tandai dibaca
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-600 dark:text-slate-400">
                Belum ada notifikasi.
            </div>
        @endforelse
    </div>

    <div>
        {{ $notifications->links() }}
    </div>
</div>
@endsection
