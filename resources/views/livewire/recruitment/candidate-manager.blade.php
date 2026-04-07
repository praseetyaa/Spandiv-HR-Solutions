<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div><h2 class="text-2xl font-bold text-slate-900 m-0">Kandidat</h2><p class="text-sm text-slate-500 mt-1 mb-0">Pipeline rekrutmen dan manajemen kandidat</p></div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Kandidat</button>
    </div>
    {{-- Pipeline Stats --}}
    <div class="grid grid-cols-5 gap-3 mb-6">
        @foreach(['applied' => ['Melamar', 'slate'], 'screening' => ['Screening', 'blue'], 'interview' => ['Interview', 'purple'], 'offering' => ['Offering', 'amber'], 'hired' => ['Diterima', 'emerald']] as $stage => [$label, $color])
            <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm text-center"><div class="text-2xl font-bold text-{{ $color }}-600">{{ $this->stats[$stage] }}</div><div class="text-xs text-slate-400 mt-1">{{ $label }}</div></div>
        @endforeach
    </div>
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[200px] max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kandidat..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
        <select wire:model.live="stageFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Stage</option><option value="applied">Melamar</option><option value="screening">Screening</option><option value="interview">Interview</option><option value="offering">Offering</option><option value="hired">Diterima</option><option value="rejected">Ditolak</option></select>
        <select wire:model.live="jobFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Lowongan</option>@foreach($this->jobs as $j)<option value="{{ $j->id }}">{{ $j->title }}</option>@endforeach</select>
    </div>
    <div class="flex gap-6">
        <div class="flex-1 min-w-0"><x-ui.card><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100">
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Kandidat</th>
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Lowongan</th>
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Stage</th>
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Sumber</th>
            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
        </tr></thead><tbody>
            @forelse($this->candidates as $c)
                <tr wire:click="selectCandidate({{ $c->id }})" class="border-b border-slate-50 hover:bg-slate-50/50 cursor-pointer {{ $selectedId === $c->id ? 'bg-brand-50' : '' }}">
                    <td class="py-3 px-4"><div><div class="font-medium text-slate-900">{{ $c->name }}</div><div class="text-xs text-slate-400">{{ $c->email }}</div></div></td>
                    <td class="py-3 px-4 text-slate-500">{{ $c->jobPosting?->title ?? '-' }}</td>
                    <td class="py-3 px-4"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full text-{{ $c->stage_color }}-600 bg-{{ $c->stage_color }}-50">{{ $c->stage_label }}</span></td>
                    <td class="py-3 px-4 text-slate-400">{{ $c->source ?? '-' }}</td>
                    <td class="py-3 px-4 text-right"><div class="flex items-center justify-end gap-1">
                        <select wire:click.stop wire:change="moveStage({{ $c->id }}, $event.target.value)" class="text-xs py-1 px-2 border border-slate-200 rounded-lg outline-none bg-white"><option value="" disabled selected>Move...</option><option value="screening">Screening</option><option value="interview">Interview</option><option value="offering">Offering</option><option value="hired">Terima</option><option value="rejected">Tolak</option></select>
                        <button wire:click.stop="delete({{ $c->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-12 text-center text-slate-400">Belum ada kandidat.</td></tr>
            @endforelse
        </tbody></table></div></x-ui.card></div>
        @if($this->selected)
            <div class="w-[320px] shrink-0"><x-ui.card>
                <h3 class="m-0 text-lg font-bold text-slate-900">{{ $this->selected->name }}</h3>
                <p class="text-sm text-slate-400 mb-4">{{ $this->selected->email }}</p>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-slate-400">Telepon</span><span class="font-medium">{{ $this->selected->phone ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Lowongan</span><span class="font-medium">{{ $this->selected->jobPosting?->title }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Sumber</span><span class="font-medium">{{ $this->selected->source ?? '-' }}</span></div>
                </div>
                @if($this->selected->interviews->count())
                    <div class="mt-4 pt-4 border-t border-slate-100"><span class="text-xs font-semibold text-slate-400 uppercase mb-2 block">Interview</span>
                        @foreach($this->selected->interviews as $iv)
                            <div class="py-2 text-sm"><div class="font-medium text-slate-900">{{ $iv->type_label }} — {{ $iv->scheduled_at->format('d M Y H:i') }}</div><div class="text-xs text-slate-400">{{ $iv->interviewer?->name }} · {{ $iv->result_label }}</div></div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card></div>
        @endif
    </div>

    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Tambah Kandidat</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Lowongan <span class="text-danger">*</span></label><select wire:model="jobId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->jobs as $j)<option value="{{ $j->id }}">{{ $j->title }}</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama <span class="text-danger">*</span></label><input type="text" wire:model="name" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Email <span class="text-danger">*</span></label><input type="email" wire:model="email" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Telepon</label><input type="text" wire:model="phone" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Sumber</label><input type="text" wire:model="source" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="LinkedIn, dll"></div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
