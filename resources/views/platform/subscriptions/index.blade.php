@extends('layouts.platform', ['pageTitle' => 'Kelola Subscription'])

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold text-white m-0">Subscription</h2>
        <p class="text-sm text-white/40 mt-1 m-0">Pantau dan kelola langganan semua tenant.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @php
            $totalActive = $subscriptions->where('status', 'active')->count();
            $totalUnpaid = $subscriptions->where('payment_status', 'unpaid')->count();
            $totalOverdue = $subscriptions->where('payment_status', 'overdue')->count();
        @endphp
        <div class="rounded-xl bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border border-white/[0.06] p-5">
            <div class="text-2xl font-bold text-white">{{ $totalActive }}</div>
            <div class="text-sm text-white/40 mt-1">Langganan Aktif</div>
        </div>
        <div class="rounded-xl bg-gradient-to-br from-amber-500/10 to-amber-600/5 border border-white/[0.06] p-5">
            <div class="text-2xl font-bold text-white">{{ $totalUnpaid }}</div>
            <div class="text-sm text-white/40 mt-1">Menunggu Pembayaran</div>
        </div>
        <div class="rounded-xl bg-gradient-to-br from-red-500/10 to-red-600/5 border border-white/[0.06] p-5">
            <div class="text-2xl font-bold text-white">{{ $totalOverdue }}</div>
            <div class="text-sm text-white/40 mt-1">Overdue</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl bg-[#161822] border border-white/[0.06] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/[0.06]">
                        <th class="text-left px-6 py-4 text-white/40 font-medium">Tenant</th>
                        <th class="text-left px-6 py-4 text-white/40 font-medium">Paket</th>
                        <th class="text-left px-6 py-4 text-white/40 font-medium">Siklus</th>
                        <th class="text-left px-6 py-4 text-white/40 font-medium">Pembayaran</th>
                        <th class="text-left px-6 py-4 text-white/40 font-medium">Status</th>
                        <th class="text-left px-6 py-4 text-white/40 font-medium">Berakhir</th>
                        <th class="text-center px-6 py-4 text-white/40 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subscriptions as $sub)
                        <tr class="border-b border-white/[0.04] hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4 font-medium text-white">{{ $sub->tenant?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-white/60">{{ $sub->plan?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $sub->billing_cycle === 'yearly' ? 'bg-violet-500/10 text-violet-400' : 'bg-sky-500/10 text-sky-400' }}">
                                    {{ ucfirst($sub->billing_cycle) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $payColors = [
                                        'paid'    => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'unpaid'  => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'overdue' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                    ];
                                    $pc = $payColors[$sub->payment_status] ?? $payColors['unpaid'];
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $pc }}">
                                    {{ ucfirst($sub->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $subColors = [
                                        'active'    => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'cancelled' => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                        'expired'   => 'bg-red-500/10 text-red-400 border-red-500/20',
                                    ];
                                    $ssc = $subColors[$sub->status] ?? $subColors['cancelled'];
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $ssc }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-white/40">{{ $sub->ends_at ? $sub->ends_at->format('d/m/Y') : '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if ($sub->payment_status !== 'paid')
                                    <form method="POST" action="{{ route('platform.subscriptions.approve', $sub) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium cursor-pointer hover:bg-emerald-500/20 transition-all" onclick="return confirm('Konfirmasi pembayaran?')">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                            Approve
                                        </button>
                                    </form>
                                @else
                                    <span class="text-white/20 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-white/30">Belum ada subscription.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($subscriptions->hasPages())
            <div class="px-6 py-4 border-t border-white/[0.06]">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
@endsection
