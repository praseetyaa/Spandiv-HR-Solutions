<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div><h2 class="text-2xl font-bold text-slate-900 m-0">Absensi</h2><p class="text-sm text-slate-500 mt-1 mb-0">Monitor kehadiran karyawan harian</p></div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Input Manual</button>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-slate-900">{{ $this->stats['total'] }}</div><div class="text-xs text-slate-400 mt-1">Total Absen</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-emerald-600">{{ $this->stats['present'] }}</div><div class="text-xs text-slate-400 mt-1">Hadir</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-amber-600">{{ $this->stats['late'] }}</div><div class="text-xs text-slate-400 mt-1">Terlambat</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-red-500">{{ $this->stats['absent'] }}</div><div class="text-xs text-slate-400 mt-1">Tidak Hadir</div></div>
    </div>
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <input type="date" wire:model.live="dateFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        <div class="relative flex-1 min-w-[200px] max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari karyawan..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Status</option><option value="present">Hadir</option><option value="late">Terlambat</option><option value="absent">Tidak Hadir</option><option value="leave">Cuti</option><option value="wfh">WFH</option></select>
    </div>
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-slate-100">
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Masuk</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Keluar</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Durasi</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
                    <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($this->attendances as $att)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                            <td class="py-3 px-4"><div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand to-[#3468B8] flex items-center justify-center text-xs font-bold text-white">{{ $att->employee->initials }}</div><div><div class="font-medium text-slate-900">{{ $att->employee->full_name }}</div><div class="text-xs text-slate-400">{{ $att->employee->department?->name ?? '-' }}</div></div></div></td>
                            <td class="py-3 px-4 font-mono text-slate-600">{{ $att->clock_in?->format('H:i') ?? '-' }}</td>
                            <td class="py-3 px-4 font-mono text-slate-600">{{ $att->clock_out?->format('H:i') ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $att->work_hours ?? '-' }}</td>
                            <td class="py-3 px-4"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $att->status === 'present' ? 'text-emerald-600 bg-emerald-50' : ($att->status === 'late' ? 'text-amber-600 bg-amber-50' : 'text-red-500 bg-red-50') }}">{{ $att->status_label }}</span></td>
                            <td class="py-3 px-4 text-right"><div class="flex items-center justify-end gap-1">
                                <button wire:click="openForm({{ $att->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                <button wire:click="delete({{ $att->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-slate-400">Tidak ada data absensi untuk tanggal ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Input' }} Absensi</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label><select wire:model="employeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }}</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal <span class="text-danger">*</span></label><input type="date" wire:model="date" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Jam Masuk</label><input type="time" wire:model="clockIn" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Jam Keluar</label><input type="time" wire:model="clockOut" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Status</label><select wire:model="status" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="present">Hadir</option><option value="late">Terlambat</option><option value="absent">Tidak Hadir</option><option value="leave">Cuti</option><option value="wfh">WFH</option><option value="half_day">Setengah Hari</option></select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Catatan</label><textarea wire:model="notes" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
