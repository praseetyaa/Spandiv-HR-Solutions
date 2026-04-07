<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div><h2 class="text-2xl font-bold text-slate-900 m-0">Jabatan</h2><p class="text-sm text-slate-500 mt-1 mb-0">Kelola posisi dan jabatan karyawan</p></div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Jabatan</button>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[200px] max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari jabatan..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
        <select wire:model.live="departmentFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Departemen</option>@foreach($this->departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select>
    </div>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-slate-100">
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Jabatan</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Departemen</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Level</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Grade</th>
                    <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
                    <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($this->positions as $pos)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                            <td class="py-3 px-4"><div class="font-medium text-slate-900">{{ $pos->title }}</div>@if(!$pos->is_active)<span class="text-[10px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded-full">NON-AKTIF</span>@endif</td>
                            <td class="py-3 px-4 text-slate-500">{{ $pos->department?->name ?? '-' }}</td>
                            <td class="py-3 px-4"><span class="text-[10px] font-bold text-brand bg-brand/10 px-2 py-0.5 rounded-full">{{ strtoupper(str_replace('_', ' ', $pos->level)) }}</span></td>
                            <td class="py-3 px-4 text-slate-500">{{ $pos->grade ?? '-' }}</td>
                            <td class="py-3 px-4 text-center font-medium">{{ $pos->employees_count }}</td>
                            <td class="py-3 px-4 text-right"><div class="flex items-center justify-end gap-1">
                                <button wire:click="openForm({{ $pos->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                <button wire:click="delete({{ $pos->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-slate-400">Belum ada jabatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Tambah' }} Jabatan</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Judul Jabatan <span class="text-danger">*</span></label><input type="text" wire:model="title" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('title')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Departemen <span class="text-danger">*</span></label><select wire:model="departmentId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Level</label><select wire:model="level" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="staff">Staff</option><option value="supervisor">Supervisor</option><option value="manager">Manager</option><option value="senior_manager">Senior Manager</option><option value="director">Director</option><option value="c_level">C-Level</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Grade</label><input type="text" wire:model="grade" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi</label><textarea wire:model="description" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="isActive" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">Aktif</span></label>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
