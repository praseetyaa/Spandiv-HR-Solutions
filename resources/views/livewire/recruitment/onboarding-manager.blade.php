<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div><h2 class="text-2xl font-bold text-slate-900 m-0">Onboarding</h2><p class="text-sm text-slate-500 mt-1 mb-0">Kelola proses onboarding karyawan baru</p></div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Mulai Onboarding</button>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-slate-900">{{ $this->stats['total'] }}</div><div class="text-xs text-slate-400 mt-1">Total</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-blue-600">{{ $this->stats['in_progress'] }}</div><div class="text-xs text-slate-400 mt-1">Berjalan</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-emerald-600">{{ $this->stats['completed'] }}</div><div class="text-xs text-slate-400 mt-1">Selesai</div></div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm"><div class="text-2xl font-bold text-red-500">{{ $this->stats['overdue'] }}</div><div class="text-xs text-slate-400 mt-1">Terlambat</div></div>
    </div>
    <div class="flex items-center gap-1 mb-6 p-1 bg-slate-100 rounded-xl w-fit">
        <button wire:click="$set('tab', 'onboardings')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'onboardings' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">👥 Onboarding</button>
        <button wire:click="$set('tab', 'templates')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'templates' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">📋 Template</button>
    </div>

    @if($tab === 'onboardings')
        <x-ui.card><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-slate-100">
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Template</th>
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Periode</th>
            <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Progress</th>
            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
        </tr></thead><tbody>
            @forelse($this->onboardings as $ob)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                    <td class="py-3 px-4"><div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand to-[#3468B8] flex items-center justify-center text-xs font-bold text-white">{{ $ob->employee->initials }}</div><div><div class="font-medium text-slate-900">{{ $ob->employee->full_name }}</div><div class="text-xs text-slate-400">{{ $ob->employee->department?->name ?? '-' }}</div></div></div></td>
                    <td class="py-3 px-4 text-slate-500">{{ $ob->template?->name ?? '-' }}</td>
                    <td class="py-3 px-4 text-slate-400 text-xs">{{ $ob->start_date->format('d/m') }} — {{ $ob->expected_end_date->format('d/m/Y') }}</td>
                    <td class="py-3 px-4"><div class="flex items-center gap-2 justify-center"><div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-brand rounded-full" style="width: {{ $ob->progress_percent }}%"></div></div><span class="text-xs font-bold text-slate-500">{{ $ob->progress_percent }}%</span></div></td>
                    <td class="py-3 px-4"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full text-{{ $ob->status_color }}-600 bg-{{ $ob->status_color }}-50">{{ $ob->status_label }}</span></td>
                    <td class="py-3 px-4 text-right"><button wire:click="deleteOnboarding({{ $ob->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button></td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-12 text-center text-slate-400">Belum ada proses onboarding.</td></tr>
            @endforelse
        </tbody></table></div></x-ui.card>
    @else
        <div class="flex justify-end mb-4"><button wire:click="openTemplateForm" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Tambah Template</button></div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->templates as $tpl)
                <x-ui.card><div class="flex items-start justify-between mb-2"><div><h4 class="m-0 text-sm font-bold text-slate-900">{{ $tpl->name }}</h4>@if($tpl->description)<p class="text-xs text-slate-400 mt-0.5 mb-0">{{ Str::limit($tpl->description, 80) }}</p>@endif</div><div class="flex gap-1"><button wire:click="openTemplateForm({{ $tpl->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button><button wire:click="deleteTemplate({{ $tpl->id }})" wire:confirm="Hapus?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button></div></div>
                <div class="flex items-center gap-3 text-xs text-slate-400 mt-2"><span>📋 {{ $tpl->tasks_count }} task</span><span>👥 {{ $tpl->onboardings_count }} digunakan</span></div></x-ui.card>
            @empty
                <div class="col-span-full text-center py-8 text-slate-400 text-sm">Belum ada template.</div>
            @endforelse
        </div>
    @endif

    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Mulai Onboarding</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label><select wire:model="employeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }}</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Template <span class="text-danger">*</span></label><select wire:model="templateId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->templates as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Mulai</label><input type="date" wire:model="startDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Target Selesai</label><input type="date" wire:model="expectedEndDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Mulai</button></div>
                </form>
            </div>
        </div>
    @endif

    @if($showTemplateForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showTemplateForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingTemplateId ? 'Edit' : 'Tambah' }} Template</h3></div>
                <form wire:submit="saveTemplate" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama <span class="text-danger">*</span></label><input type="text" wire:model="templateName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi</label><textarea wire:model="templateDescription" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showTemplateForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
