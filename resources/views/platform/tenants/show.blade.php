@extends('layouts.platform', ['pageTitle' => 'Detail Tenant — ' . $tenant->name])

@section('content')
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-white/40 mb-6">
        <a href="{{ route('platform.tenants.index') }}" class="text-white/40 no-underline hover:text-violet-400">Tenant</a>
        <span>/</span>
        <span class="text-white/70">{{ $tenant->name }}</span>
    </div>

    {{-- Company Header --}}
    <div class="rounded-xl bg-[#161822] border border-white/[0.06] p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-violet-500/30 to-indigo-500/30 border border-violet-500/20 flex items-center justify-center text-2xl font-bold text-violet-400 shrink-0">
                    {{ strtoupper(substr($tenant->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white m-0">{{ $tenant->name }}</h2>
                    <div class="flex items-center gap-3 mt-1">
                        <code class="text-violet-400/70 text-xs bg-violet-500/10 px-2 py-0.5 rounded">{{ $tenant->subdomain }}.hrapp.id</code>
                        @php
                            $statusColors = [
                                'active'    => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                'trial'     => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                'suspended' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                'cancelled' => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                            ];
                            $sc = $statusColors[$tenant->status] ?? $statusColors['cancelled'];
                        @endphp
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $sc }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if ($tenant->status === 'active')
                    <form method="POST" action="{{ route('platform.tenants.suspend', $tenant) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-medium cursor-pointer hover:bg-red-500/20 transition-all" onclick="return confirm('Suspend tenant ini?')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                            Suspend
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('platform.tenants.activate', $tenant) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium cursor-pointer hover:bg-emerald-500/20 transition-all">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                            Aktifkan
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('platform.impersonate.start', $tenant) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-orange-500/10 border border-orange-500/20 text-orange-400 text-sm font-medium cursor-pointer hover:bg-orange-500/20 transition-all">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                        Impersonate
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Info Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Subscription Info --}}
        <div class="rounded-xl bg-[#161822] border border-white/[0.06] p-6">
            <h3 class="text-white/40 text-xs uppercase tracking-wider font-semibold m-0 mb-4">Subscription</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-white/50 text-sm">Paket</span>
                    <span class="text-white font-medium text-sm">{{ $tenant->plan?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/50 text-sm">Harga / Bulan</span>
                    <span class="text-white font-medium text-sm">Rp {{ number_format($tenant->plan?->price_monthly ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/50 text-sm">Max Karyawan</span>
                    <span class="text-white font-medium text-sm">{{ $tenant->plan?->max_employees ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/50 text-sm">Max User</span>
                    <span class="text-white font-medium text-sm">{{ $tenant->plan?->max_users ?? '-' }}</span>
                </div>
                @if ($tenant->trial_ends_at)
                    <div class="flex justify-between">
                        <span class="text-white/50 text-sm">Trial Berakhir</span>
                        <span class="text-amber-400 font-medium text-sm">{{ $tenant->trial_ends_at->format('d/m/Y') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Usage Stats --}}
        <div class="rounded-xl bg-[#161822] border border-white/[0.06] p-6">
            <h3 class="text-white/40 text-xs uppercase tracking-wider font-semibold m-0 mb-4">Penggunaan</h3>
            <div class="space-y-4">
                @php
                    $empMax = $tenant->plan?->max_employees ?? 50;
                    $empPct = $empMax > 0 ? min(($employeeCount / $empMax) * 100, 100) : 0;
                    $userMax = $tenant->plan?->max_users ?? 10;
                    $userPct = $userMax > 0 ? min(($userCount / $userMax) * 100, 100) : 0;
                @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-white/50">Karyawan</span>
                        <span class="text-white font-medium">{{ $employeeCount }} / {{ $empMax }}</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-white/5">
                        <div class="h-full rounded-full transition-all {{ $empPct > 80 ? 'bg-red-500' : 'bg-violet-500' }}" style="width: {{ $empPct }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-white/50">User</span>
                        <span class="text-white font-medium">{{ $userCount }} / {{ $userMax }}</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-white/5">
                        <div class="h-full rounded-full transition-all {{ $userPct > 80 ? 'bg-red-500' : 'bg-sky-500' }}" style="width: {{ $userPct }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Settings --}}
        <div class="rounded-xl bg-[#161822] border border-white/[0.06] p-6">
            <h3 class="text-white/40 text-xs uppercase tracking-wider font-semibold m-0 mb-4">Pengaturan</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-white/50 text-sm">Timezone</span>
                    <span class="text-white font-medium text-sm">{{ $tenant->settings?->timezone ?? 'Asia/Jakarta' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/50 text-sm">Currency</span>
                    <span class="text-white font-medium text-sm">{{ $tenant->settings?->currency ?? 'IDR' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/50 text-sm">Bahasa</span>
                    <span class="text-white font-medium text-sm">{{ $tenant->settings?->language ?? 'id' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/50 text-sm">Payroll Cutoff</span>
                    <span class="text-white font-medium text-sm">Tanggal {{ $tenant->settings?->payroll_cutoff_day ?? 25 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/50 text-sm">Dibuat</span>
                    <span class="text-white font-medium text-sm">{{ $tenant->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
