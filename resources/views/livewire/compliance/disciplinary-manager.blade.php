<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Catatan Disipliner</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola peringatan, skorsing, dan catatan disiplin karyawan</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Catatan
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-slate-100">
            <div class="text-2xl font-bold text-slate-900">{{ $this->stats['total'] }}</div>
            <div class="text-xs text-slate-400">Total Catatan</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-amber-100">
            <div class="text-2xl font-bold text-amber-600">{{ $this->stats['active_warnings'] }}</div>
            <div class="text-xs text-slate-400">Peringatan Aktif</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-red-100">
            <div class="text-2xl font-bold text-red-600">{{ $this->stats['sp3'] }}</div>
            <div class="text-xs text-slate-400">SP-3</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari karyawan..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        </div>
        <select wire:model.live="typeFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Tipe</option>
            <option value="warning">Peringatan</option>
            <option value="suspension">Skorsing</option>
            <option value="termination">PHK</option>
        </select>
        <select wire:model.live="levelFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Level</option>
            <option value="verbal">Teguran Lisan</option>
            <option value="sp1">SP-1</option>
            <option value="sp2">SP-2</option>
            <option value="sp3">SP-3</option>
        </select>
    </div>

    {{-- Table --}}
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-slate-100">
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Level</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Tipe</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Pelanggaran</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Tanggal</th>
                    <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
                    <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($this->records as $record)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-700">{{ $record->employee->initials }}</div>
                                    <div>
                                        <div class="font-medium text-slate-900">{{ $record->employee->full_name }}</div>
                                        <div class="text-xs text-slate-400">{{ $record->employee->department?->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $levelColors = ['verbal' => 'bg-slate-100 text-slate-600', 'sp1' => 'bg-amber-100 text-amber-700', 'sp2' => 'bg-orange-100 text-orange-700', 'sp3' => 'bg-red-100 text-red-700'];
                                @endphp
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $levelColors[$record->level] ?? 'bg-slate-100 text-slate-600' }}">{{ strtoupper($record->level === 'verbal' ? 'Lisan' : $record->level) }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">{{ $record->type_label }}</td>
                            <td class="py-3 px-4 text-slate-600 max-w-[200px] truncate">{{ $record->violation }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $record->incident_date->format('d M Y') }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($record->is_active)
                                    <span class="text-[10px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">AKTIF</span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">EXPIRED</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openForm({{ $record->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                    <button wire:click="delete({{ $record->id }})" wire:confirm="Hapus catatan ini?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="text-3xl mb-2">✅</div>
                            <div class="font-medium text-slate-600 mb-1">Belum Ada Catatan</div>
                            <div class="text-sm">Tidak ada catatan disipliner.</div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    {{-- Form Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[560px] max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Tambah' }} Catatan Disipliner</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label><select wire:model="employeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih Karyawan...</option>@foreach($this->employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach</select>@error('employeeId')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tipe</label><select wire:model="type" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="warning">Peringatan</option><option value="suspension">Skorsing</option><option value="termination">PHK</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Level</label><select wire:model="level" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="verbal">Teguran Lisan</option><option value="sp1">SP-1</option><option value="sp2">SP-2</option><option value="sp3">SP-3</option></select></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Pelanggaran <span class="text-danger">*</span></label><textarea wire:model="violation" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea>@error('violation')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tindakan <span class="text-danger">*</span></label><textarea wire:model="actionTaken" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea>@error('actionTaken')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal Insiden <span class="text-danger">*</span></label><input type="date" wire:model="incidentDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Berlaku Sampai</label><input type="date" wire:model="warningExpiresAt" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
