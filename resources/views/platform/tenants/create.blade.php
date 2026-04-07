@extends('layouts.platform', ['pageTitle' => 'Tambah Tenant Baru'])

@section('content')
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-white/40 mb-6">
        <a href="{{ route('platform.tenants.index') }}" class="text-white/40 no-underline hover:text-violet-400">Tenant</a>
        <span>/</span>
        <span class="text-white/70">Tambah Baru</span>
    </div>

    <div class="max-w-2xl">
        <div class="rounded-xl bg-[#161822] border border-white/[0.06] overflow-hidden">
            <div class="px-6 py-4 border-b border-white/[0.06]">
                <h3 class="text-white font-semibold m-0">Registrasi Tenant Baru</h3>
                <p class="text-sm text-white/40 mt-1 m-0">Isi data perusahaan dan buat akun owner.</p>
            </div>

            <form method="POST" action="{{ route('platform.tenants.store') }}" class="p-6 space-y-5">
                @csrf

                {{-- Company Section --}}
                <div class="space-y-4">
                    <h4 class="text-white/60 text-xs uppercase tracking-wider font-semibold m-0 pb-2 border-b border-white/[0.06]">Data Perusahaan</h4>

                    <div>
                        <label class="block text-sm text-white/70 font-medium mb-1.5">Nama Perusahaan <span class="text-red-400">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required
                               class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50 transition-colors"
                               placeholder="PT Contoh Indonesia">
                        @error('company_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-white/70 font-medium mb-1.5">Subdomain <span class="text-red-400">*</span></label>
                            <div class="flex">
                                <input type="text" name="subdomain" value="{{ old('subdomain') }}" required
                                       class="flex-1 px-4 py-2.5 rounded-l-lg bg-white/5 border border-r-0 border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50 transition-colors"
                                       placeholder="contoh">
                                <span class="px-3 py-2.5 rounded-r-lg bg-white/[0.03] border border-white/[0.08] text-white/30 text-sm">.hrapp.id</span>
                            </div>
                            @error('subdomain') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-white/70 font-medium mb-1.5">Custom Domain</label>
                            <input type="text" name="domain" value="{{ old('domain') }}"
                                   class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50 transition-colors"
                                   placeholder="hr.contoh.com">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-white/70 font-medium mb-1.5">Paket <span class="text-red-400">*</span></label>
                            <select name="plan_id" required class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm focus:outline-none focus:border-violet-500/50 transition-colors">
                                <option value="">Pilih paket...</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}" class="bg-[#161822]" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} — Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}/bulan
                                    </option>
                                @endforeach
                            </select>
                            @error('plan_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-white/70 font-medium mb-1.5">Brand Color</label>
                            <input type="color" name="brand_color" value="{{ old('brand_color', '#2B5BA8') }}"
                                   class="w-full h-[42px] px-1 py-1 rounded-lg bg-white/5 border border-white/[0.08] cursor-pointer">
                        </div>
                    </div>
                </div>

                {{-- Owner Section --}}
                <div class="space-y-4">
                    <h4 class="text-white/60 text-xs uppercase tracking-wider font-semibold m-0 pb-2 border-b border-white/[0.06]">Akun Owner</h4>

                    <div>
                        <label class="block text-sm text-white/70 font-medium mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                        <input type="text" name="owner_name" value="{{ old('owner_name') }}" required
                               class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50 transition-colors"
                               placeholder="Budi Santoso">
                        @error('owner_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-white/70 font-medium mb-1.5">Email <span class="text-red-400">*</span></label>
                            <input type="email" name="owner_email" value="{{ old('owner_email') }}" required
                                   class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50 transition-colors"
                                   placeholder="budi@contoh.com">
                            @error('owner_email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-white/70 font-medium mb-1.5">Password <span class="text-red-400">*</span></label>
                            <input type="password" name="owner_password" required
                                   class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/[0.08] text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-500/50 transition-colors"
                                   placeholder="Min. 8 karakter">
                            @error('owner_password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3 pt-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold border-none cursor-pointer hover:bg-violet-500 transition-all shadow-lg shadow-violet-600/20">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                        Buat Tenant
                    </button>
                    <a href="{{ route('platform.tenants.index') }}" class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/[0.08] text-white/60 text-sm no-underline hover:bg-white/[0.08] transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
