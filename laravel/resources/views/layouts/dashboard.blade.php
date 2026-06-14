@extends('layouts.app')

@section('content')
<div x-data="{ sidebarOpen: window.innerWidth >= 768 }" class="flex h-screen bg-slate-50 dark:bg-slate-950">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 shadow-lg transform transition-transform duration-200 lg:translate-x-0 lg:static lg:inset-auto"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="h-full flex flex-col">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-lg text-slate-900 dark:text-white">Puskesmas</span>
                </div>

                <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                    <svg class="w-5 h-5" style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Menu -->
            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-2">
                @if (auth()->user()?->isAdmin())

                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5" style="width:20px;height:20px;min-width:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 16l7-4M9 9l7 4"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <div class="pt-4 pb-2">
                        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Master Data</p>
                    </div>

                    <a href="{{ route('admin.polis.index') }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('admin.polis.*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5" style="width:20px;height:20px;min-width:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4z"></path>
                        </svg>
                        <span>Poli</span>
                    </a>

                    <a href="{{ route('admin.doctors.index') }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('admin.doctors.*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5" style="width:20px;height:20px;min-width:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <span>Dokter</span>
                    </a>

                    <a href="{{ route('admin.schedules.index') }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('admin.schedules.*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5" style="width:20px;height:20px;min-width:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Jadwal Dokter</span>
                    </a>

                    <div class="pt-4 pb-2">
                        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Manajemen</p>
                    </div>

                    <a href="{{ route('admin.bookings.index') }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('admin.bookings.*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5" style="width:20px;height:20px;min-width:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"></path>
                        </svg>
                        <span>Antrian</span>
                    </a>

                    <a href="{{ route('admin.patients.index') }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('admin.patients.*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5" style="width:20px;height:20px;min-width:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM5 20a6 6 0 1112 0v2H5v-2z"></path>
                        </svg>
                        <span>Pasien</span>
                    </a>

                    <a href="{{ route('admin.reports.index') }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5" style="width:20px;height:20px;min-width:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        <span>Laporan Statistik</span>
                    </a>

                @elseif (auth()->user()?->isPatient())

                    <a href="{{ route('patient.dashboard') }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('patient.dashboard') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5" style="width:20px;height:20px;min-width:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 16l7-4M9 9l7 4"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('patient.booking.create') }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('patient.booking.*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5" style="width:20px;height:20px;min-width:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Booking Baru</span>
                    </a>

                    <a href="{{ route('patient.ai.index') }}"
                       class="flex items-center gap-3 w-full px-4 py-3 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('patient.ai.*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                        <svg class="w-5 h-5" style="width:20px;height:20px;min-width:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 4v-4z"></path>
                        </svg>
                        <span>Tanya AI</span>
                    </a>

                @endif
            </nav>

            <!-- User Profile -->
            <div class="border-t border-slate-200 dark:border-slate-700 p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 text-left text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div x-show="sidebarOpen && window.innerWidth < 1024" @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/50 lg:hidden transition-opacity"
        style="display: none;"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <div class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-6">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                <svg class="w-5 h-5" style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <div class="flex items-center gap-4">
                <a href="{{ route('notifications.index') }}" class="relative p-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <svg class="w-5 h-5" style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>

                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute -top-1 -right-1 flex min-w-5 h-5 items-center justify-center rounded-full bg-red-500 px-1 text-[11px] font-bold text-white">
                            {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div class="flex-1 overflow-auto">
            <div class="p-6">
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ $errors->first() }}</p>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                @endif

                @yield('page-content')
            </div>
        </div>
    </div>
</div>
@endsection
