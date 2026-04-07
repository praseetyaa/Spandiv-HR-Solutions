<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div><h2 class="text-2xl font-bold text-slate-900 m-0">Bonus</h2><p class="text-sm text-slate-500 mt-1 mb-0">Kelola skema dan distribusi bonus karyawan</p></div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Distribusi Bonus</button>
    </div>
    <div class="flex items-center gap-1 mb-6 p-1 bg-slate-100 rounded-xl w-fit">
        <button wire:click="$set('tab', 'distributions')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'distributions' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">🎁 Distribusi</button>
        <button wire:click="$set('tab', 'schemes')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'schemes' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">📋 Skema</button>
    </div>

    @if($tab === 'distributions')
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <div class="relative flex-1 min-w-[200px] max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari karyawan..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
            <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"><option value="">Semua Status</option><option value="pending">Menunggu</option><option value="approved">Disetujui</option><option value="paid">Dibayar</option></select>
        </div>
        <x-ui.card><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100">
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Skema</th>
            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Nominal</th>
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
        </tr></thead><tbody>
            @forelse($this->distributions as $b)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                    <td class="py-3 px-4"><div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-xs font-bold text-white">{{ $b->employee->initials }}</div><div><div class="font-medium text-slate-900">{{ $b->employee->full_name }}</div><div class="text-xs text-slate-400">{{ $b->employee->department?->name ?? '-' }}</div></div></div></td>
                    <td class="py-3 px-4 text-slate-500">{{ $b->scheme?->name ?? '-' }}</td>
                    <td class="py-3 px-4 text-right font-mono font-bold text-brand">Rp {{ number_format($b->amount) }}</td>
                    <td class="py-3 px-4"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $b->status === 'paid' ? 'text-emerald-600 bg-emerald-50' : ($b->status === 'approved' ? 'text-blue-600 bg-blue-50' : 'text-amber-600 bg-amber-50') }}">{{ $b->status_label }}</span></td>
                    <td class="py-3 px-4 text-right"><div class="flex items-center justify-end gap-1">
                        @if($b->status === 'pending')<button wire:click="approve({{ $b->id }})" class="p-1.5 rounded-lg border-none bg-emerald-50 text-emerald-500 cursor-pointer hover:bg-emerald-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></button>@endif
                        <button wire:click="deleteBonus({{ $b->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-12 text-center text-slate-400">Belum ada distribusi bonus.</td></tr>
            @endforelse
        </tbody></table></div></x-ui.card>
    @else
        <div class="flex justify-end mb-4"><button wire:click="openSchemeForm" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Tambah Skema</button></div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->schemes as $scheme)
                <x-ui.card><div class="flex items-start justify-between mb-2"><div><h4 class="m-0 text-sm font-bold text-slate-900">{{ $scheme->name }}</h4><span class="text-xs text-slate-400">{{ $scheme->type_label }} · {{ $scheme->period_label }}</span></div><div class="flex gap-1"><button wire:click="openSchemeForm({{ $scheme->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button><button wire:click="deleteScheme({{ $scheme->id }})" wire:confirm="Hapus?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button></div></div>
                <div class="text-xs text-slate-400 mt-2">{{ $scheme->employee_bonuses_count }} distribusi</div></x-ui.card>
            @empty
                <div class="col-span-full text-center py-8 text-slate-400 text-sm">Belum ada skema.</div>
            @endforelse
        </div>
    @endif

    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Distribusi Bonus</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label><select wire:model="employeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }}</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Skema</label><select wire:model="schemeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">— Tanpa skema —</option>@foreach($this->schemes as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nominal <span class="text-danger">*</span></label><input type="number" wire:model="amount" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" step="0.01"></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Catatan</label><textarea wire:model="notes" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif

    @if($showSchemeForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showSchemeForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingSchemeId ? 'Edit' : 'Tambah' }} Skema</h3></div>
                <form wire:submit="saveScheme" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama <span class="text-danger">*</span></label><input type="text" wire:model="schemeName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div class="grid grid-cols-2 gap-4"><div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tipe</label><select wire:model="schemeType" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="fixed">Nominal Tetap</option><option value="percentage">Persentase</option><option value="performance_based">Berbasis Performa</option></select></div><div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Periode</label><select wire:model="period" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="monthly">Bulanan</option><option value="quarterly">Kuartalan</option><option value="annually">Tahunan</option><option value="one_time">Sekali</option></select></div></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showSchemeForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
