<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div><h2 class="text-2xl font-bold text-slate-900 m-0">Departemen</h2><p class="text-sm text-slate-500 mt-1 mb-0">Kelola struktur departemen perusahaan</p></div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Departemen</button>
    </div>

    <div class="mb-4"><div class="relative max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari departemen..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div></div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($this->departments as $dept)
            <x-ui.card>
                <div class="flex items-start justify-between mb-3">
                    <div><h4 class="m-0 text-sm font-bold text-slate-900">{{ $dept->name }}</h4><span class="text-xs text-slate-400">{{ $dept->code }}</span></div>
                    <div class="flex gap-1">
                        <button wire:click="openForm({{ $dept->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                        <button wire:click="delete({{ $dept->id }})" wire:confirm="Hapus?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-xs text-slate-400">
                    <span>👥 {{ $dept->employees_count }} karyawan</span>
                    @if($dept->parent)<span>📁 {{ $dept->parent->name }}</span>@endif
                    @if($dept->headEmployee)<span>👤 {{ $dept->headEmployee->full_name }}</span>@endif
                </div>
                @if(!$dept->is_active)<span class="mt-2 inline-block text-[10px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">NON-AKTIF</span>@endif
            </x-ui.card>
        @empty
            <div class="col-span-full text-center py-8 text-slate-400 text-sm">Belum ada departemen.</div>
        @endforelse
    </div>

    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Tambah' }} Departemen</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama <span class="text-danger">*</span></label><input type="text" wire:model="name" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('name')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kode <span class="text-danger">*</span></label><input type="text" wire:model="code" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Induk Departemen</label><select wire:model="parentId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">— Tidak ada —</option>@foreach($this->parentOptions as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kepala Departemen</label><select wire:model="headEmployeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">— Pilih —</option>@foreach($this->employeeOptions as $e)<option value="{{ $e->id }}">{{ $e->full_name }}</option>@endforeach</select></div>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="isActive" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">Aktif</span></label>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
