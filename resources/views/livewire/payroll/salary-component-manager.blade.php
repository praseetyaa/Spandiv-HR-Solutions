<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div><h2 class="text-2xl font-bold text-slate-900 m-0">Komponen Gaji</h2><p class="text-sm text-slate-500 mt-1 mb-0">Kelola komponen tunjangan, potongan, dan grade gaji</p></div>
    </div>
    <div class="flex items-center gap-1 mb-6 p-1 bg-slate-100 rounded-xl w-fit">
        <button wire:click="$set('tab', 'components')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'components' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">💰 Komponen</button>
        <button wire:click="$set('tab', 'grades')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'grades' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">📊 Grade</button>
    </div>

    @if($tab === 'components')
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div class="relative max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari komponen..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div>
            <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Tambah Komponen</button>
        </div>
        <x-ui.card>
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100">
                <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Nama</th>
                <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Tipe</th>
                <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Perhitungan</th>
                <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Default</th>
                <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Info</th>
                <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
            </tr></thead><tbody>
                @forelse($this->components as $comp)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3 px-4 font-medium text-slate-900">{{ $comp->name }}</td>
                        <td class="py-3 px-4"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $comp->type === 'allowance' ? 'text-emerald-600 bg-emerald-50' : 'text-red-500 bg-red-50' }}">{{ $comp->type_label }}</span></td>
                        <td class="py-3 px-4 text-slate-500">{{ ucfirst($comp->calculation_type) }}</td>
                        <td class="py-3 px-4 text-right font-mono">{{ number_format($comp->default_amount) }}</td>
                        <td class="py-3 px-4 text-center"><div class="flex items-center justify-center gap-1">@if($comp->is_taxable)<span class="text-[9px] bg-amber-50 text-amber-600 px-1.5 py-0.5 rounded font-bold">PAJAK</span>@endif @if($comp->is_mandatory)<span class="text-[9px] bg-red-50 text-red-500 px-1.5 py-0.5 rounded font-bold">WAJIB</span>@endif</div></td>
                        <td class="py-3 px-4 text-right"><div class="flex items-center justify-end gap-1">
                            <button wire:click="openForm({{ $comp->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                            <button wire:click="deleteComponent({{ $comp->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-12 text-center text-slate-400">Belum ada komponen gaji.</td></tr>
                @endforelse
            </tbody></table></div>
        </x-ui.card>
    @else
        <div class="flex justify-end mb-4"><button wire:click="openGradeForm" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Tambah Grade</button></div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->grades as $grade)
                <x-ui.card>
                    <div class="flex items-start justify-between mb-2"><div><h4 class="m-0 text-sm font-bold text-slate-900">{{ $grade->name }}</h4><span class="text-xs text-slate-400">{{ $grade->code }} · Level {{ $grade->level }}</span></div><div class="flex gap-1"><button wire:click="openGradeForm({{ $grade->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button><button wire:click="deleteGrade({{ $grade->id }})" wire:confirm="Hapus?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button></div></div>
                    <div class="text-xs text-slate-400 mt-2">{{ $grade->employee_salaries_count }} karyawan</div>
                </x-ui.card>
            @empty
                <div class="col-span-full text-center py-8 text-slate-400 text-sm">Belum ada grade gaji.</div>
            @endforelse
        </div>
    @endif

    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Tambah' }} Komponen</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama <span class="text-danger">*</span></label><input type="text" wire:model="name" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tipe</label><select wire:model="type" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="allowance">Tunjangan</option><option value="deduction">Potongan</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Perhitungan</label><select wire:model="calculationType" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="fixed">Fixed</option><option value="percentage">Persentase</option><option value="formula">Formula</option></select></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nominal Default</label><input type="number" wire:model="defaultAmount" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" step="0.01"></div>
                    <div class="flex gap-4"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="isTaxable" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">Kena Pajak</span></label><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="isMandatory" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">Wajib</span></label></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif

    @if($showGradeForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showGradeForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[420px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingGradeId ? 'Edit' : 'Tambah' }} Grade</h3></div>
                <form wire:submit="saveGrade" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4"><div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama <span class="text-danger">*</span></label><input type="text" wire:model="gradeName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div><div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kode <span class="text-danger">*</span></label><input type="text" wire:model="gradeCode" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Level</label><input type="number" wire:model="gradeLevel" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" min="1"></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showGradeForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
