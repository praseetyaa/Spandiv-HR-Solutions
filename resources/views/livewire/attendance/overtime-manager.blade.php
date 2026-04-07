<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div><h2 class="text-2xl font-bold text-slate-900 m-0">Lembur</h2><p class="text-sm text-slate-500 mt-1 mb-0">Kelola pengajuan dan persetujuan lembur</p></div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Ajukan Lembur</button>
    </div>
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-amber-600">{{ $this->stats['pending'] }}</div><div class="text-xs text-slate-400 mt-1">Menunggu</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-emerald-600">{{ $this->stats['approved'] }}</div><div class="text-xs text-slate-400 mt-1">Disetujui</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-brand">{{ number_format($this->stats['total_hours'], 1) }}</div><div class="text-xs text-slate-400 mt-1">Total Jam</div></div>
    </div>
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[200px] max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari karyawan..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Status</option><option value="pending">Menunggu</option><option value="approved">Disetujui</option><option value="rejected">Ditolak</option></select>
    </div>
    <x-ui.card>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100">
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Tanggal</th>
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Waktu</th>
            <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Jam</th>
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
        </tr></thead><tbody>
            @forelse($this->requests as $req)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                    <td class="py-3 px-4"><div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand to-[#3468B8] flex items-center justify-center text-xs font-bold text-white">{{ $req->employee->initials }}</div><div><div class="font-medium text-slate-900">{{ $req->employee->full_name }}</div><div class="text-xs text-slate-400">{{ $req->employee->department?->name ?? '-' }}</div></div></div></td>
                    <td class="py-3 px-4 text-slate-500">{{ $req->date->format('d M Y') }}</td>
                    <td class="py-3 px-4 font-mono text-slate-600">{{ $req->start_time }} — {{ $req->end_time }}</td>
                    <td class="py-3 px-4 text-center font-bold text-brand">{{ $req->hours }}h</td>
                    <td class="py-3 px-4"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $req->status === 'approved' ? 'text-emerald-600 bg-emerald-50' : ($req->status === 'pending' ? 'text-amber-600 bg-amber-50' : 'text-red-500 bg-red-50') }}">{{ $req->status_label }}</span></td>
                    <td class="py-3 px-4 text-right"><div class="flex items-center justify-end gap-1">
                        @if($req->status === 'pending')
                            <button wire:click="approve({{ $req->id }})" class="p-1.5 rounded-lg border-none bg-emerald-50 text-emerald-500 cursor-pointer hover:bg-emerald-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></button>
                            <button wire:click="reject({{ $req->id }})" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                        @endif
                        <button wire:click="delete({{ $req->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-400 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-12 text-center text-slate-400">Belum ada pengajuan lembur.</td></tr>
            @endforelse
        </tbody></table></div>
    </x-ui.card>

    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Ajukan Lembur</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label><select wire:model="employeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }}</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal <span class="text-danger">*</span></label><input type="date" wire:model="date" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div class="grid grid-cols-3 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Mulai</label><input type="time" wire:model="startTime" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Selesai</label><input type="time" wire:model="endTime" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Jam</label><input type="number" wire:model="hours" step="0.5" min="0.5" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Alasan <span class="text-danger">*</span></label><textarea wire:model="reason" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Ajukan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
