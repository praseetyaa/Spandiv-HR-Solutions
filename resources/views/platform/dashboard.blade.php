@extends('layouts.platform', ['pageTitle' => 'Platform Dashboard'])

@section('content')
    {{-- Welcome Banner --}}
    <div class="rounded-2xl px-6 py-8 md:px-10 md:py-8 mb-7 relative overflow-hidden bg-gradient-to-r from-violet-600/20 via-indigo-600/20 to-purple-600/20 border border-violet-500/10">
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-violet-500/5"></div>
        <div class="absolute -bottom-16 right-20 w-36 h-36 rounded-full bg-indigo-500/5"></div>
        <div class="relative z-[1]">
            <h2 class="m-0 mb-2 text-white text-2xl font-bold">Platform Overview 🛡️</h2>
            <p class="m-0 text-white/50 text-[15px]">
                {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }} — Kelola semua tenant dan subscription dari sini.
            </p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-7">
        @php
            $platformStats = [
                ['label' => 'Total Tenant', 'value' => $stats['total_tenants'] ?? 0, 'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>', 'color' => 'violet', 'bg' => 'from-violet-500/20 to-violet-600/10', 'text' => 'text-violet-400'],
                ['label' => 'Tenant Aktif', 'value' => $stats['active_tenants'] ?? 0, 'icon' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>', 'color' => 'emerald', 'bg' => 'from-emerald-500/20 to-emerald-600/10', 'text' => 'text-emerald-400'],
                ['label' => 'Total Users', 'value' => $stats['total_users'] ?? 0, 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>', 'color' => 'sky', 'bg' => 'from-sky-500/20 to-sky-600/10', 'text' => 'text-sky-400'],
                ['label' => 'MRR', 'value' => 'Rp ' . number_format($stats['mrr'] ?? 0, 0, ',', '.'), 'icon' => '<line x1="12" x2="12" y1="1" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>', 'color' => 'amber', 'bg' => 'from-amber-500/20 to-amber-600/10', 'text' => 'text-amber-400'],
            ];
        @endphp

        @foreach ($platformStats as $stat)
            <div class="rounded-xl bg-gradient-to-br {{ $stat['bg'] }} border border-white/[0.06] p-5 transition-all duration-300 hover:-translate-y-0.5 hover:border-white/[0.1]">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center {{ $stat['text'] }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            {!! $stat['icon'] !!}
                        </svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-white mb-1">{{ $stat['value'] }}</div>
                <div class="text-sm text-white/40">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-6">
        {{-- Recent Tenants --}}
        <div class="rounded-xl bg-[#161822] border border-white/[0.06] overflow-hidden">
            <div class="px-6 py-4 border-b border-white/[0.06] flex items-center justify-between">
                <h3 class="text-white font-semibold m-0">Tenant Terbaru</h3>
                <a href="{{ route('platform.tenants.index') }}" class="text-sm text-violet-400 no-underline hover:text-violet-300">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/[0.04]">
                            <th class="text-left px-6 py-3 text-white/40 font-medium">Perusahaan</th>
                            <th class="text-left px-6 py-3 text-white/40 font-medium">Subdomain</th>
                            <th class="text-left px-6 py-3 text-white/40 font-medium">Status</th>
                            <th class="text-left px-6 py-3 text-white/40 font-medium">Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTenants as $tenant)
                            <tr class="border-b border-white/[0.04] hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-3">
                                    <a href="{{ route('platform.tenants.show', $tenant) }}" class="text-white no-underline hover:text-violet-300 font-medium">
                                        {{ $tenant->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-white/50">{{ $tenant->subdomain }}</td>
                                <td class="px-6 py-3">
                                    @php
                                        $statusColors = [
                                            'active'    => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                            'trial'     => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                            'suspended' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                            'cancelled' => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                        ];
                                        $sc = $statusColors[$tenant->status] ?? $statusColors['cancelled'];
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $sc }}">
                                        {{ ucfirst($tenant->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-white/40">{{ $tenant->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-white/30">Belum ada tenant terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick Stats Sidebar --}}
        <div class="space-y-6">
            {{-- Tenant Status Breakdown --}}
            <div class="rounded-xl bg-[#161822] border border-white/[0.06] p-6">
                <h3 class="text-white font-semibold m-0 mb-4">Status Tenant</h3>
                <div class="space-y-3">
                    @php
                        $statusBreakdown = [
                            ['label' => 'Active', 'value' => $stats['active_tenants'] ?? 0, 'color' => 'bg-emerald-500'],
                            ['label' => 'Trial', 'value' => $stats['trial_tenants'] ?? 0, 'color' => 'bg-amber-500'],
                            ['label' => 'Suspended', 'value' => $stats['suspended_tenants'] ?? 0, 'color' => 'bg-red-500'],
                        ];
                        $totalT = max(array_sum(array_column($statusBreakdown, 'value')), 1);
                    @endphp
                    @foreach ($statusBreakdown as $status)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-white/60">{{ $status['label'] }}</span>
                                <span class="text-white font-medium">{{ $status['value'] }}</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-white/5 overflow-hidden">
                                <div class="{{ $status['color'] }} h-full rounded-full transition-all duration-500"
                                     style="width: {{ ($status['value'] / $totalT) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="rounded-xl bg-[#161822] border border-white/[0.06] p-6">
                <h3 class="text-white font-semibold m-0 mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('platform.tenants.create') }}" class="flex items-center gap-3 p-3 rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-300 no-underline hover:bg-violet-500/20 transition-all text-sm">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                        Tambah Tenant Baru
                    </a>
                    <a href="{{ route('platform.plans.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/[0.03] border border-white/[0.06] text-white/60 no-underline hover:bg-white/[0.06] transition-all text-sm">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        Kelola Paket
                    </a>
                    <a href="{{ route('platform.subscriptions.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/[0.03] border border-white/[0.06] text-white/60 no-underline hover:bg-white/[0.06] transition-all text-sm">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/></svg>
                        Lihat Subscription
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
