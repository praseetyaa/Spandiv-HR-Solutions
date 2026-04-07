<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Pengaturan</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Konfigurasi profil perusahaan, lokalisasi, dan fitur</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 font-medium flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="saveSettings" class="space-y-6">
        {{-- Company Profile --}}
        <x-ui.card title="Profil Perusahaan">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama Perusahaan</label><input type="text" wire:model="companyName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Email</label><input type="email" wire:model="companyEmail" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Telepon</label><input type="text" wire:model="companyPhone" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Alamat</label><input type="text" wire:model="companyAddress" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
            </div>
        </x-ui.card>

        {{-- Locale --}}
        <x-ui.card title="Lokalisasi">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Timezone</label><select wire:model="timezone" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="Asia/Jakarta">Asia/Jakarta (WIB)</option><option value="Asia/Makassar">Asia/Makassar (WITA)</option><option value="Asia/Jayapura">Asia/Jayapura (WIT)</option></select></div>
                <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Bahasa</label><select wire:model="locale" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="id">Bahasa Indonesia</option><option value="en">English</option></select></div>
                <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Format Tanggal</label><select wire:model="dateFormat" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="d/m/Y">DD/MM/YYYY</option><option value="m/d/Y">MM/DD/YYYY</option><option value="Y-m-d">YYYY-MM-DD</option></select></div>
                <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Mata Uang</label><select wire:model="currency" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="IDR">IDR (Rupiah)</option><option value="USD">USD (Dollar)</option></select></div>
            </div>
        </x-ui.card>

        {{-- Feature Toggles --}}
        <x-ui.card title="Fitur Modul">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors">
                    <input type="checkbox" wire:model="featureRecruitment" class="accent-brand w-5 h-5">
                    <div><div class="text-sm font-semibold text-slate-700">Rekrutmen</div><div class="text-[11px] text-slate-400">Job posting & onboarding</div></div>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors">
                    <input type="checkbox" wire:model="featurePsychTest" class="accent-brand w-5 h-5">
                    <div><div class="text-sm font-semibold text-slate-700">Tes Psikologi</div><div class="text-[11px] text-slate-400">Psych test management</div></div>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors">
                    <input type="checkbox" wire:model="featurePayroll" class="accent-brand w-5 h-5">
                    <div><div class="text-sm font-semibold text-slate-700">Payroll</div><div class="text-[11px] text-slate-400">Penggajian & bonus</div></div>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors">
                    <input type="checkbox" wire:model="featureLearning" class="accent-brand w-5 h-5">
                    <div><div class="text-sm font-semibold text-slate-700">Learning</div><div class="text-[11px] text-slate-400">Kursus & sertifikasi</div></div>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors">
                    <input type="checkbox" wire:model="featureEngagement" class="accent-brand w-5 h-5">
                    <div><div class="text-sm font-semibold text-slate-700">Engagement</div><div class="text-[11px] text-slate-400">Survei & pengumuman</div></div>
                </label>
            </div>
        </x-ui.card>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 rounded-xl border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                💾 Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
