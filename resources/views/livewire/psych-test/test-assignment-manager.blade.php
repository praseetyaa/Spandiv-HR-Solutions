<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Penugasan Tes</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola penugasan tes psikologi ke kandidat</p>
        </div>
        <button wire:click="openAssignForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tugaskan Tes
        </button>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php $st = $this->stats; @endphp
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-slate-700">{{ $st['total'] }}</div><div class="text-xs font-medium text-slate-400 mt-1">Total</div></div></x-ui.card>
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-amber-600">{{ $st['pending'] }}</div><div class="text-xs font-medium text-amber-500 mt-1">Pending</div></div></x-ui.card>
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-blue-600">{{ $st['in_progress'] }}</div><div class="text-xs font-medium text-blue-500 mt-1">In Progress</div></div></x-ui.card>
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-emerald-600">{{ $st['completed'] }}</div><div class="text-xs font-medium text-emerald-500 mt-1">Selesai</div></div></x-ui.card>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kandidat..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Status</option><option value="pending">⏳ Pending</option><option value="in_progress">🔄 In Progress</option><option value="completed">✅ Completed</option><option value="expired">⏰ Expired</option></select>
        <select wire:model.live="testFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Tes</option>@foreach($this->tests as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select>
    </div>

    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-slate-100">
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Kandidat</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Tes</th>
                    <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Deadline</th>
                    <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Attempt</th>
                    <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
                    <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($this->assignments as $a)
                        @php $sb = match($a->status) { 'pending'=>['Pending','bg-amber-50 text-amber-600'],'in_progress'=>['In Progress','bg-blue-50 text-blue-600'],'completed'=>['Completed','bg-emerald-50 text-emerald-600'],'expired'=>['Expired','bg-red-50 text-red-500'],default=>[$a->status,'bg-slate-100 text-slate-500'] }; @endphp
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                            <td class="py-3 px-4"><div class="font-medium text-slate-900">{{ $a->candidate?->name ?? '-' }}</div><div class="text-xs text-slate-400">{{ $a->candidate?->email ?? '' }}</div></td>
                            <td class="py-3 px-4"><div class="text-slate-700">{{ $a->test?->name ?? '-' }}</div></td>
                            <td class="py-3 px-4 text-center text-xs text-slate-500">{{ $a->deadline_at?->format('d M Y') }}</td>
                            <td class="py-3 px-4 text-center"><span class="text-xs font-bold text-slate-600">{{ $a->attempt_count }}/{{ $a->max_attempts }}</span></td>
                            <td class="py-3 px-4 text-center"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $sb[1] }}">{{ $sb[0] }}</span></td>
                            <td class="py-3 px-4 text-right"><button wire:click="deleteAssignment({{ $a->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-slate-400"><div class="text-3xl mb-2">📝</div><div class="font-medium text-slate-600 mb-1">Belum Ada Penugasan</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    @if($showAssignForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showAssignForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Tugaskan Tes</h3></div>
                <form wire:submit="saveAssignment" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kandidat <span class="text-danger">*</span></label><select wire:model="assignCandidateId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih</option>@foreach($this->candidates as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tes <span class="text-danger">*</span></label><select wire:model="assignTestId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih</option>@foreach($this->tests as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deadline</label><input type="date" wire:model="assignDeadline" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Max Attempts</label><input type="number" wire:model="assignMaxAttempts" min="1" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showAssignForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Tugaskan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
