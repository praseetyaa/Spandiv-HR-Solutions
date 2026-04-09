<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Profil Saya</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola informasi akun dan keamanan Anda</p>
        </div>
    </div>

    @if(session('profile_success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 font-medium flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('profile_success') }}
        </div>
    @endif

    @if(session('password_success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 font-medium flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('password_success') }}
        </div>
    @endif

    <div class="space-y-6">
        {{-- Profile Info --}}
        <form wire:submit="updateProfile">
            <x-ui.card title="Informasi Akun">
                <div class="flex items-center gap-5 mb-6">
                    <div class="profile-avatar w-16 h-16 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-lg font-bold text-slate-900">{{ auth()->user()->name }}</div>
                        <div class="text-sm text-slate-500">{{ auth()->user()->email }}</div>
                        <div class="text-xs text-slate-400 mt-1">
                            {{ auth()->user()->isPlatformUser() ? 'Platform Admin' : 'Tenant User' }}
                            · Bergabung {{ auth()->user()->created_at?->translatedFormat('d M Y') ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama Lengkap</label>
                        <input type="text" wire:model="name" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none focus:border-brand focus:ring-2 focus:ring-brand/10">
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Email</label>
                        <input type="email" wire:model="email" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none focus:border-brand focus:ring-2 focus:ring-brand/10">
                        @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="px-5 py-2.5 rounded-xl border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                        Simpan Profil
                    </button>
                </div>
            </x-ui.card>
        </form>

        {{-- Change Password --}}
        <form wire:submit="updatePassword">
            <x-ui.card title="Ubah Password">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Password Saat Ini</label>
                        <input type="password" wire:model="current_password" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none focus:border-brand focus:ring-2 focus:ring-brand/10">
                        @error('current_password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Password Baru</label>
                        <input type="password" wire:model="password" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none focus:border-brand focus:ring-2 focus:ring-brand/10">
                        @error('password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Konfirmasi Password</label>
                        <input type="password" wire:model="password_confirmation" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none focus:border-brand focus:ring-2 focus:ring-brand/10">
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="px-5 py-2.5 rounded-xl border-none bg-slate-800 text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                        🔒 Ubah Password
                    </button>
                </div>
            </x-ui.card>
        </form>

        {{-- Account Info (read-only) --}}
        <x-ui.card title="Info Akun">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Login Terakhir</label>
                    <div class="py-2.5 px-3.5 bg-slate-50 rounded-[10px] text-sm text-slate-600">
                        {{ auth()->user()->last_login_at?->translatedFormat('d M Y H:i') ?? 'Belum pernah' }}
                    </div>
                </div>
                <div>
                    <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Status</label>
                    <div class="py-2.5 px-3.5 bg-slate-50 rounded-[10px] text-sm">
                        @if(auth()->user()->is_active)
                            <span class="text-emerald-600 font-medium">✅ Aktif</span>
                        @else
                            <span class="text-red-600 font-medium">❌ Nonaktif</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tenant</label>
                    <div class="py-2.5 px-3.5 bg-slate-50 rounded-[10px] text-sm text-slate-600">
                        {{ auth()->user()->tenant->name ?? '-' }}
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
