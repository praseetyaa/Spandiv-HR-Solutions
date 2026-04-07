<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div><h2 class="text-2xl font-bold text-slate-900 m-0">Lowongan</h2><p class="text-sm text-slate-500 mt-1 mb-0">Kelola lowongan pekerjaan dan publikasi</p></div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Buat Lowongan</button>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-slate-900">{{ $this->stats['total'] }}</div><div class="text-xs text-slate-400 mt-1">Total Lowongan</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-emerald-600">{{ $this->stats['published'] }}</div><div class="text-xs text-slate-400 mt-1">Dipublikasi</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-blue-600">{{ $this->stats['closed'] }}</div><div class="text-xs text-slate-400 mt-1">Ditutup</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-purple-600">{{ $this->stats['candidates'] }}</div><div class="text-xs text-slate-400 mt-1">Total Kandidat</div></div>
    </div>
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[200px] max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari lowongan..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Status</option><option value="draft">Draft</option><option value="published">Dipublikasi</option><option value="closed">Ditutup</option></select>
    </div>
    <div class="flex gap-6">
        <div class="flex-1 min-w-0">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($this->postings as $job)
                    <div wire:click="selectPosting({{ $job->id }})" class="bg-white rounded-xl p-5 border cursor-pointer transition-all duration-200 hover:shadow-md {{ $selectedId === $job->id ? 'border-brand shadow-md' : 'border-slate-100' }}">
                        <div class="flex items-start justify-between mb-2"><div><h4 class="m-0 text-sm font-bold text-slate-900">{{ $job->title }}</h4><span class="text-xs text-slate-400">{{ $job->department?->name }} · {{ $job->employment_type_label }}</span></div><span class="text-[10px] font-bold px-2 py-0.5 rounded-full text-{{ $job->status_color }}-600 bg-{{ $job->status_color }}-50">{{ $job->status_label }}</span></div>
                        <div class="flex items-center gap-3 text-xs text-slate-400 mt-3"><span>👥 {{ $job->candidates_count }} kandidat</span><span>📋 {{ $job->openings }} posisi</span>@if($job->close_date)<span>📅 {{ $job->close_date->format('d M Y') }}</span>@endif</div>
                        <div class="flex gap-1 mt-3"><button wire:click.stop="openForm({{ $job->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button><button wire:click.stop="delete({{ $job->id }})" wire:confirm="Hapus?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button></div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-slate-400 text-sm">Belum ada lowongan.</div>
                @endforelse
            </div>
        </div>
        @if($this->selected)
            <div class="w-[340px] shrink-0"><x-ui.card>
                <h3 class="m-0 text-lg font-bold text-slate-900 mb-1">{{ $this->selected->title }}</h3>
                <p class="text-sm text-slate-400 mb-4">{{ $this->selected->department?->name }} · {{ $this->selected->position?->title }}</p>
                <div class="space-y-3 text-sm">
                    <div><span class="text-xs font-semibold text-slate-400 uppercase">Deskripsi</span><p class="text-slate-600 mt-1 mb-0 text-sm leading-relaxed">{{ Str::limit($this->selected->description, 200) }}</p></div>
                    <div><span class="text-xs font-semibold text-slate-400 uppercase">Persyaratan</span><p class="text-slate-600 mt-1 mb-0 text-sm leading-relaxed">{{ Str::limit($this->selected->requirements, 200) }}</p></div>
                    @if($this->selected->salary_min)<div class="flex justify-between"><span class="text-slate-400">Gaji</span><span class="font-medium">{{ number_format($this->selected->salary_min) }} — {{ number_format($this->selected->salary_max) }}</span></div>@endif
                </div>
                @if($this->selected->candidates->count())
                    <div class="mt-4 pt-4 border-t border-slate-100"><span class="text-xs font-semibold text-slate-400 uppercase mb-2 block">Kandidat ({{ $this->selected->candidates->count() }})</span>
                        @foreach($this->selected->candidates->take(5) as $c)
                            <div class="flex items-center justify-between py-1.5"><span class="text-sm text-slate-700">{{ $c->name }}</span><span class="text-[10px] font-bold px-2 py-0.5 rounded-full text-{{ $c->stage_color }}-600 bg-{{ $c->stage_color }}-50">{{ $c->stage_label }}</span></div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card></div>
        @endif
    </div>

    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[600px] max-h-[85vh] overflow-y-auto">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Buat' }} Lowongan</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Judul <span class="text-danger">*</span></label><input type="text" wire:model="title" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Departemen <span class="text-danger">*</span></label><select wire:model="departmentId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Jabatan <span class="text-danger">*</span></label><select wire:model="positionId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->positions as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach</select></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi <span class="text-danger">*</span></label><textarea wire:model="description" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Persyaratan <span class="text-danger">*</span></label><textarea wire:model="requirements" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div class="grid grid-cols-3 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tipe</label><select wire:model="employmentType" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="permanent">Tetap</option><option value="contract">Kontrak</option><option value="internship">Magang</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Posisi</label><input type="number" wire:model="openings" min="1" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Status</label><select wire:model="status" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="draft">Draft</option><option value="published">Publikasi</option><option value="closed">Tutup</option></select></div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
