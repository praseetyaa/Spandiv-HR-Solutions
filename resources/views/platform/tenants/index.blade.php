@extends('layouts.platform', ['pageTitle' => 'Kelola Tenant'])

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-white m-0">Daftar Tenant</h2>
            <p class="text-sm text-white/40 mt-1 m-0">Kelola semua perusahaan yang terdaftar di platform.</p>
        </div>
        <a href="{{ route('platform.tenants.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold no-underline hover:bg-violet-500 transition-all shadow-lg shadow-violet-600/20">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/>
            </svg>
            Tambah Tenant
        </a>
    </div>

    {{-- Filters --}}
    <div class="rounded-xl bg-[#161822] border border-white/[0.06] p-4 mb-4" x-data="{ search: '', status: '' }">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-white/30" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" placeholder="Cari tenant..." class="w-full pl-9 pr-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50 transition-colors">
            </div>
            <select class="px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white/70 text-sm focus:outline-none focus:border-violet-500/50 appearance-none">
                <option value="">Semua Status</option>
                <option value="active">Active</option>
                <option value="trial">Trial</option>
                <option value="suspended">Suspended</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl bg-[#161822] border border-white/[0.06] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/[0.06]">
                        <th class="text-left px-6 py-4 text-white/40 font-medium">Perusahaan</th>
                        <th class="text-left px-6 py-4 text-white/40 font-medium">Subdomain</th>
                        <th class="text-left px-6 py-4 text-white/40 font-medium">Paket</th>
                        <th class="text-left px-6 py-4 text-white/40 font-medium">Status</th>
                        <th class="text-left px-6 py-4 text-white/40 font-medium">Dibuat</th>
                        <th class="text-center px-6 py-4 text-white/40 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $tenants = \App\Models\Tenant::with('plan')->latest()->paginate(20);
                    @endphp

                    @forelse ($tenants as $tenant)
                        <tr class="border-b border-white/[0.04] hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('platform.tenants.show', $tenant) }}" class="text-white no-underline hover:text-violet-300 font-medium">
                                    {{ $tenant->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-violet-400/70 text-xs bg-violet-500/10 px-2 py-1 rounded">{{ $tenant->subdomain }}</code>
                            </td>
                            <td class="px-6 py-4 text-white/60">{{ $tenant->plan?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
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
                            <td class="px-6 py-4 text-white/40">{{ $tenant->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('platform.tenants.show', $tenant) }}"
                                       class="p-2 rounded-lg hover:bg-white/5 text-white/40 hover:text-violet-400 transition-colors no-underline"
                                       title="Detail">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    @if ($tenant->status === 'active')
                                        <form method="POST" action="{{ route('platform.tenants.suspend', $tenant) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-lg hover:bg-red-500/10 text-white/40 hover:text-red-400 transition-colors border-none bg-transparent cursor-pointer" title="Suspend" onclick="return confirm('Suspend tenant ini?')">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('platform.tenants.activate', $tenant) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-lg hover:bg-emerald-500/10 text-white/40 hover:text-emerald-400 transition-colors border-none bg-transparent cursor-pointer" title="Activate">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('platform.impersonate.start', $tenant) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-lg hover:bg-orange-500/10 text-white/40 hover:text-orange-400 transition-colors border-none bg-transparent cursor-pointer" title="Impersonate">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-white/30">
                                <div class="flex flex-col items-center gap-3">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-white/10">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                                    </svg>
                                    <span>Belum ada tenant.</span>
                                    <a href="{{ route('platform.tenants.create') }}" class="text-violet-400 no-underline hover:text-violet-300">Buat yang pertama →</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tenants->hasPages())
            <div class="px-6 py-4 border-t border-white/[0.06]">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
@endsection
