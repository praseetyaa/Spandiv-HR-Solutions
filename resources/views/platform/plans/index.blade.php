@extends('layouts.platform', ['pageTitle' => 'Kelola Paket'])

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-white m-0">Paket / Plan</h2>
            <p class="text-sm text-white/40 mt-1 m-0">Kelola paket langganan yang tersedia untuk tenant.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-6">
        {{-- Plans List --}}
        <div class="rounded-xl bg-[#161822] border border-white/[0.06] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/[0.06]">
                            <th class="text-left px-6 py-4 text-white/40 font-medium">Nama Paket</th>
                            <th class="text-left px-6 py-4 text-white/40 font-medium">Harga/Bulan</th>
                            <th class="text-left px-6 py-4 text-white/40 font-medium">Max Karyawan</th>
                            <th class="text-left px-6 py-4 text-white/40 font-medium">Max User</th>
                            <th class="text-left px-6 py-4 text-white/40 font-medium">Tenant</th>
                            <th class="text-left px-6 py-4 text-white/40 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($plans as $plan)
                            <tr class="border-b border-white/[0.04] hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $plan->name }}</div>
                                    <div class="text-xs text-white/30 mt-0.5">{{ $plan->slug }}</div>
                                </td>
                                <td class="px-6 py-4 text-white/70">Rp {{ number_format($plan->price_monthly ?? $plan->price ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-white/70">{{ $plan->max_employees }}</td>
                                <td class="px-6 py-4 text-white/70">{{ $plan->max_users }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 text-xs bg-violet-500/10 text-violet-400 rounded-full">{{ $plan->tenants_count ?? 0 }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($plan->status === 'active')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Active</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-500/10 text-slate-400 border border-slate-500/20">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-white/30">Belum ada paket.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Create Plan Form --}}
        <div class="rounded-xl bg-[#161822] border border-white/[0.06] overflow-hidden h-fit">
            <div class="px-6 py-4 border-b border-white/[0.06]">
                <h3 class="text-white font-semibold m-0 text-sm">Buat Paket Baru</h3>
            </div>
            <form method="POST" action="{{ route('platform.plans.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">Nama <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50" placeholder="Professional">
                </div>
                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">Slug <span class="text-red-400">*</span></label>
                    <input type="text" name="slug" required class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50" placeholder="professional">
                </div>
                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">Harga/Bulan (Rp) <span class="text-red-400">*</span></label>
                    <input type="number" name="price" required min="0" class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50" placeholder="500000">
                </div>
                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">Billing Cycle <span class="text-red-400">*</span></label>
                    <select name="billing_cycle" required class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm focus:outline-none focus:border-violet-500/50">
                        <option value="monthly" class="bg-[#161822]">Monthly</option>
                        <option value="yearly" class="bg-[#161822]">Yearly</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-white/70 font-medium mb-1.5">Max Kary. <span class="text-red-400">*</span></label>
                        <input type="number" name="max_employees" required min="1" class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50" placeholder="50">
                    </div>
                    <div>
                        <label class="block text-sm text-white/70 font-medium mb-1.5">Max User <span class="text-red-400">*</span></label>
                        <input type="number" name="max_users" required min="1" class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50" placeholder="10">
                    </div>
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold border-none cursor-pointer hover:bg-violet-500 transition-all shadow-lg shadow-violet-600/20">
                    Simpan Paket
                </button>
            </form>
        </div>
    </div>
@endsection
