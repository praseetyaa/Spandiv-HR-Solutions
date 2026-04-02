@extends('layouts.app')

@section('content')
    {{-- Welcome Banner --}}
    <div class="welcome-banner rounded-2xl px-6 py-8 md:px-10 md:py-8 mb-7 relative overflow-hidden">
        {{-- Decorative circles --}}
        <div class="banner-circle-1"></div>
        <div class="banner-circle-2"></div>

        <div class="relative z-[1]">
            <h2 class="m-0 mb-2 text-white text-2xl font-bold">
                Selamat Datang, {{ auth()->user()->name }}! 👋
            </h2>
            <p class="m-0 text-white/60 text-[15px]">
                {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }} — Kelola HR Anda dengan mudah dan efisien.
            </p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-5 mb-7">
        @foreach ($stats as $stat)
            <x-ui.stat-card
                :label="$stat['label']"
                :value="$stat['value']"
                :icon="$stat['icon']"
                :color="$stat['color']"
            />
        @endforeach
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-6">
        {{-- Quick Actions --}}
        <x-ui.card title="Aksi Cepat">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @php
                    $quickActions = [
                        ['label' => 'Tambah Karyawan', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/>', 'color' => '#2B5BA8'],
                        ['label' => 'Buat Pengumuman', 'icon' => '<path d="m3 11 18-5v12L3 13v-2z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>', 'color' => '#F59E0B'],
                        ['label' => 'Proses Payroll', 'icon' => '<rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/><path d="M16 14h2"/>', 'color' => '#16A34A'],
                        ['label' => 'Buka Lowongan', 'icon' => '<rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>', 'color' => '#8B5CF6'],
                    ];
                @endphp

                @foreach ($quickActions as $action)
                    <a href="#" class="flex items-center gap-3 p-4 rounded-xl border border-slate-100 no-underline transition-all duration-200 bg-white hover:-translate-y-px hover:shadow-md">
                        <div
                            class="w-10 h-10 rounded-[10px] flex items-center justify-center shrink-0"
                            style="background: {{ $action['color'] }}15;"
                        >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $action['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                {!! $action['icon'] !!}
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700">{{ $action['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </x-ui.card>

        {{-- Recent Activity --}}
        <x-ui.card title="Aktivitas Terbaru">
            <div class="flex flex-col gap-4">
                @php
                    $activities = [
                        ['text' => 'Sistem dimulai — Selamat datang!', 'time' => 'Baru saja', 'icon' => '🚀'],
                        ['text' => 'Database berhasil di-seed', 'time' => 'Baru saja', 'icon' => '🗃️'],
                        ['text' => 'Konfigurasi selesai', 'time' => 'Baru saja', 'icon' => '⚙️'],
                    ];
                @endphp

                @foreach ($activities as $activity)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-[10px] bg-slate-50 flex items-center justify-center text-base shrink-0">
                            {{ $activity['icon'] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="m-0 text-[13px] text-slate-700 leading-snug">
                                {{ $activity['text'] }}
                            </p>
                            <p class="mt-0.5 mb-0 text-xs text-slate-400">
                                {{ $activity['time'] }}
                            </p>
                        </div>
                    </div>
                @endforeach

                <div class="text-center pt-1">
                    <a href="#" class="text-[13px] text-brand no-underline font-medium">
                        Lihat semua →
                    </a>
                </div>
            </div>
        </x-ui.card>
    </div>
@endsection
