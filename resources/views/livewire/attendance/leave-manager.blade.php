<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div><h2 class="text-2xl font-bold text-slate-900 m-0">Cuti & Izin</h2><p class="text-sm text-slate-500 mt-1 mb-0">Kelola pengajuan cuti dan tipe cuti</p></div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Ajukan Cuti</button>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-slate-900">{{ $this->stats['total'] }}</div><div class="text-xs text-slate-400 mt-1">Total</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-amber-600">{{ $this->stats['pending'] }}</div><div class="text-xs text-slate-400 mt-1">Menunggu</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-emerald-600">{{ $this->stats['approved'] }}</div><div class="text-xs text-slate-400 mt-1">Disetujui</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-red-500">{{ $this->stats['rejected'] }}</div><div class="text-xs text-slate-400 mt-1">Ditolak</div></div>
    </div>
    <div class="flex items-center gap-1 mb-6 p-1 bg-slate-100 rounded-xl w-fit">
        <button wire:click="$set('tab', 'requests')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'requests' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">📋 Pengajuan</button>
        <button wire:click="$set('tab', 'types')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'types' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">🏷️ Tipe Cuti</button>
    </div>

    @if($tab === 'requests')
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <div class="relative flex-1 min-w-[200px] max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari karyawan..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
            <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Status</option><option value="pending">Menunggu</option><option value="approved">Disetujui</option><option value="rejected">Ditolak</option></select>
        </div>
        <x-ui.card>
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100">
                <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
                <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Tipe Cuti</th>
                <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Periode</th>
                <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Hari</th>
                <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
                <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
            </tr></thead><tbody>
                @forelse($this->requests as $req)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3 px-4"><div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand to-[#3468B8] flex items-center justify-center text-xs font-bold text-white">{{ $req->employee->initials }}</div><div><div class="font-medium text-slate-900">{{ $req->employee->full_name }}</div><div class="text-xs text-slate-400">{{ $req->employee->department?->name ?? '-' }}</div></div></div></td>
                        <td class="py-3 px-4 text-slate-600">{{ $req->leaveType->name }}</td>
                        <td class="py-3 px-4 text-slate-500">{{ $req->start_date->format('d/m') }} — {{ $req->end_date->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 text-center font-bold">{{ $req->total_days }}</td>
                        <td class="py-3 px-4"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $req->status === 'approved' ? 'text-emerald-600 bg-emerald-50' : ($req->status === 'pending' ? 'text-amber-600 bg-amber-50' : 'text-red-500 bg-red-50') }}">{{ $req->status_label }}</span></td>
                        <td class="py-3 px-4 text-right"><div class="flex items-center justify-end gap-1">
                            @if($req->status === 'pending')
                                <button wire:click="approve({{ $req->id }})" class="p-1.5 rounded-lg border-none bg-emerald-50 text-emerald-500 cursor-pointer hover:bg-emerald-100 transition-colors" title="Setujui"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></button>
                                <button wire:click="reject({{ $req->id }})" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors" title="Tolak"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                            @endif
                            <button wire:click="delete({{ $req->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-400 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-12 text-center text-slate-400">Belum ada pengajuan cuti.</td></tr>
                @endforelse
            </tbody></table></div>
        </x-ui.card>
    @else
        <div class="flex justify-end mb-4"><button wire:click="openTypeForm" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Tambah Tipe</button></div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->leaveTypes as $type)
                <x-ui.card>
                    <div class="flex items-start justify-between mb-2"><div><h4 class="m-0 text-sm font-bold text-slate-900">{{ $type->name }}</h4><span class="text-xs text-slate-400">{{ $type->code }}</span></div><div class="flex gap-1"><button wire:click="openTypeForm({{ $type->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button><button wire:click="deleteType({{ $type->id }})" wire:confirm="Hapus?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button></div></div>
                    <div class="flex items-center gap-3 mt-3 text-xs text-slate-400"><span>📅 {{ $type->days_per_year }} hari/tahun</span>@if($type->is_paid)<span class="text-emerald-500 font-semibold">BERBAYAR</span>@else<span class="text-red-400">TIDAK BERBAYAR</span>@endif<span>{{ $type->leave_requests_count }} request</span></div>
                </x-ui.card>
            @empty
                <div class="col-span-full text-center py-8 text-slate-400 text-sm">Belum ada tipe cuti.</div>
            @endforelse
        </div>
    @endif

    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Ajukan Cuti</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label><select wire:model="employeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }}</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tipe Cuti <span class="text-danger">*</span></label><select wire:model="leaveTypeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->leaveTypes as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Mulai <span class="text-danger">*</span></label><input type="date" wire:model="startDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Selesai <span class="text-danger">*</span></label><input type="date" wire:model="endDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Jumlah Hari</label><input type="number" wire:model="totalDays" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" min="1"></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Alasan</label><textarea wire:model="reason" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Ajukan</button></div>
                </form>
            </div>
        </div>
    @endif

    @if($showTypeForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showTypeForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingTypeId ? 'Edit' : 'Tambah' }} Tipe Cuti</h3></div>
                <form wire:submit="saveType" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4"><div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama <span class="text-danger">*</span></label><input type="text" wire:model="typeName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div><div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kode <span class="text-danger">*</span></label><input type="text" wire:model="typeCode" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Hari per Tahun</label><input type="number" wire:model="daysPerYear" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" min="0"></div>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="isPaid" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">Cuti Berbayar</span></label>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showTypeForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
