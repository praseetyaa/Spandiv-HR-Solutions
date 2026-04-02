<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Sertifikasi</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola sertifikasi dan pelacakan expiry karyawan</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="openEmpCertForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Record
            </button>
        </div>
    </div>

    {{-- Alert Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="p-4 rounded-xl bg-white border border-slate-100">
            <div class="text-2xl font-extrabold text-slate-900">{{ $this->records->count() }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Total Sertifikasi</div>
        </div>
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-100">
            <div class="text-2xl font-extrabold text-amber-600">{{ $this->expiringCount }}</div>
            <div class="text-xs text-amber-600 mt-0.5">⚠️ Segera Expired (30 hari)</div>
        </div>
        <div class="p-4 rounded-xl bg-red-50 border border-red-100">
            <div class="text-2xl font-extrabold text-red-600">{{ $this->expiredCount }}</div>
            <div class="text-xs text-red-600 mt-0.5">🚨 Sudah Expired</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 mb-6 p-1 bg-slate-100 rounded-xl w-fit">
        <button wire:click="$set('tab', 'records')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'records' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">
            📋 Record Karyawan
        </button>
        <button wire:click="$set('tab', 'master')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'master' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">
            🏅 Master Sertifikasi
        </button>
    </div>

    @if($tab === 'records')
        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <div class="relative flex-1 min-w-[200px] max-w-[320px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari sertifikasi..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            </div>
            <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
                <option value="">Semua Status</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="revoked">Revoked</option>
            </select>
        </div>

        {{-- Records Table --}}
        <x-ui.card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Sertifikasi</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Penerbit</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Berlaku</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->records as $rec)
                            @php
                                $daysLeft = $rec->days_until_expiry;
                                $statusBadge = match(true) {
                                    $rec->status === 'revoked' => ['danger', 'Revoked'],
                                    $rec->is_expired => ['danger', 'Expired'],
                                    $rec->is_expiring_soon => ['warning', "{$daysLeft}d left"],
                                    default => ['success', 'Active'],
                                };
                            @endphp
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-700">{{ $rec->employee->initials }}</div>
                                        <div>
                                            <div class="font-medium text-slate-900">{{ $rec->employee->full_name }}</div>
                                            <div class="text-xs text-slate-400">{{ $rec->employee->department?->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-medium text-slate-900">{{ $rec->name }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $rec->issuing_body ?? '-' }}</td>
                                <td class="py-3 px-4 text-slate-500">
                                    {{ $rec->issued_date->format('d M Y') }}
                                    @if($rec->expires_date) → {{ $rec->expires_date->format('d M Y') }} @endif
                                </td>
                                <td class="py-3 px-4">
                                    <x-ui.badge :type="$statusBadge[0]" size="xs">{{ $statusBadge[1] }}</x-ui.badge>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="openEmpCertForm({{ $rec->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                        <button wire:click="deleteEmpCert({{ $rec->id }})" wire:confirm="Hapus record ini?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-12 text-center text-slate-400">Belum ada record sertifikasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @else
        {{-- Master Certifications --}}
        <div class="flex justify-end mb-4">
            <button wire:click="openCertForm" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Tambah Sertifikasi</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->certifications as $cert)
                <x-ui.card>
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-lg">🏅</div>
                            <div>
                                <h4 class="m-0 text-sm font-bold text-slate-900">{{ $cert->name }}</h4>
                                <span class="text-xs text-slate-400">{{ $cert->issuing_body ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button wire:click="openCertForm({{ $cert->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                            <button wire:click="deleteCert({{ $cert->id }})" wire:confirm="Hapus?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mt-3 text-xs text-slate-400">
                        @if($cert->validity_months)
                            <span>Valid {{ $cert->validity_months }} bulan</span>
                        @endif
                        @if($cert->is_mandatory) <span class="text-red-500 font-bold">Wajib</span> @endif
                        <span>{{ $cert->employee_certifications_count }} holder</span>
                    </div>
                </x-ui.card>
            @empty
                <div class="col-span-full text-center py-8 text-slate-400 text-sm">Belum ada master sertifikasi.</div>
            @endforelse
        </div>
    @endif

    {{-- Cert Master Form Modal --}}
    @if($showCertForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showCertForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingCertId ? 'Edit' : 'Tambah' }} Sertifikasi</h3></div>
                <form wire:submit="saveCert" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama <span class="text-danger">*</span></label><input type="text" wire:model="certName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('certName') <p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p> @enderror</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Penerbit</label><input type="text" wire:model="certIssuingBody" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Masa Berlaku (bulan)</label><input type="number" wire:model="certValidityMonths" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" min="1"></div>
                    </div>
                    <div class="flex items-center gap-2"><input type="checkbox" wire:model="certIsMandatory" class="accent-brand w-4 h-4 cursor-pointer"><label class="text-sm text-slate-700 font-medium cursor-pointer">Sertifikasi Wajib</label></div>
                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" wire:click="$set('showCertForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Employee Cert Form Modal --}}
    @if($showEmpCertForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showEmpCertForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[540px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingEmpCertId ? 'Edit' : 'Tambah' }} Sertifikasi Karyawan</h3></div>
                <form wire:submit="saveEmpCert" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label>
                        <select wire:model="empCertEmployeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white" {{ $editingEmpCertId ? 'disabled' : '' }}>
                            <option value="">Pilih Karyawan...</option>
                            @foreach($this->employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama Sertifikasi <span class="text-danger">*</span></label><input type="text" wire:model="empCertName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Penerbit</label><input type="text" wire:model="empCertIssuingBody" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">No. Sertifikat</label><input type="text" wire:model="empCertNumber" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal Terbit <span class="text-danger">*</span></label><input type="date" wire:model="empCertIssuedDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal Expired</label><input type="date" wire:model="empCertExpiresDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" wire:click="$set('showEmpCertForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
