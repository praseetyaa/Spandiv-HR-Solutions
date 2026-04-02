<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Benefit Karyawan</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola tipe benefit, plan, dan enrollment karyawan</p>
        </div>
        <button wire:click="openEnrollForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Daftarkan Benefit
        </button>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 mb-6 p-1 bg-slate-100 rounded-xl w-fit">
        <button wire:click="$set('tab', 'enrollment')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'enrollment' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">👥 Enrollment</button>
        <button wire:click="$set('tab', 'plans')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'plans' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">📋 Plans</button>
        <button wire:click="$set('tab', 'types')" class="px-4 py-2 text-sm font-medium rounded-lg border-none cursor-pointer transition-all {{ $tab === 'types' ? 'bg-white text-slate-900 shadow-sm' : 'bg-transparent text-slate-500 hover:text-slate-700' }}">🏷️ Tipe Benefit</button>
    </div>

    @if($tab === 'enrollment')
        <div class="mb-4"><div class="relative max-w-[320px]"><svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg><input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari karyawan..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white"></div></div>
        <x-ui.card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-slate-100">
                        <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Karyawan</th>
                        <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Plan</th>
                        <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Tipe</th>
                        <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Kontribusi</th>
                        <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Berlaku Sejak</th>
                        <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                    </tr></thead>
                    <tbody>
                        @forelse($this->enrollments as $enroll)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                                <td class="py-3 px-4"><div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-700">{{ $enroll->employee->initials }}</div><div><div class="font-medium text-slate-900">{{ $enroll->employee->full_name }}</div><div class="text-xs text-slate-400">{{ $enroll->employee->department?->name ?? '-' }}</div></div></div></td>
                                <td class="py-3 px-4 font-medium text-slate-900">{{ $enroll->plan->name }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $enroll->plan->benefitType->category_label }}</td>
                                <td class="py-3 px-4 text-right"><span class="text-xs text-slate-400">EE:</span> <span class="font-medium">{{ number_format($enroll->employee_contribution) }}</span> <span class="text-xs text-slate-300">|</span> <span class="text-xs text-slate-400">ER:</span> <span class="font-medium">{{ number_format($enroll->employer_contribution) }}</span></td>
                                <td class="py-3 px-4 text-slate-500">{{ $enroll->effective_date->format('d M Y') }}</td>
                                <td class="py-3 px-4 text-right"><div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEnrollForm({{ $enroll->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                    <button wire:click="deleteEnroll({{ $enroll->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                </div></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-12 text-center text-slate-400">Belum ada enrollment benefit.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @elseif($tab === 'plans')
        <div class="flex justify-end mb-4"><button wire:click="openPlanForm" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Tambah Plan</button></div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->plans as $plan)
                <x-ui.card>
                    <div class="flex items-start justify-between mb-2">
                        <div><h4 class="m-0 text-sm font-bold text-slate-900">{{ $plan->name }}</h4><span class="text-xs text-slate-400">{{ $plan->benefitType->name }} · {{ $plan->provider ?? 'N/A' }}</span></div>
                        <div class="flex gap-1"><button wire:click="openPlanForm({{ $plan->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button><button wire:click="deletePlan({{ $plan->id }})" wire:confirm="Hapus?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button></div>
                    </div>
                    <div class="flex items-center gap-3 mt-3 text-xs text-slate-400">
                        @if($plan->coverage_amount)<span>Rp {{ number_format($plan->coverage_amount) }}</span>@endif
                        <span>{{ ucfirst($plan->coverage_type) }}</span>
                        <span>{{ $plan->employee_benefits_count }} enrolled</span>
                    </div>
                </x-ui.card>
            @empty
                <div class="col-span-full text-center py-8 text-slate-400 text-sm">Belum ada plan.</div>
            @endforelse
        </div>
    @else
        <div class="flex justify-end mb-4"><button wire:click="openTypeForm" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Tambah Tipe</button></div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->types as $type)
                <x-ui.card>
                    <div class="flex items-start justify-between mb-2">
                        <div><h4 class="m-0 text-sm font-bold text-slate-900">{{ $type->name }}</h4><span class="text-xs text-slate-400">{{ $type->category_label }}</span></div>
                        <div class="flex gap-1"><button wire:click="openTypeForm({{ $type->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button><button wire:click="deleteType({{ $type->id }})" wire:confirm="Hapus?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button></div>
                    </div>
                    <div class="flex items-center gap-2 mt-3">
                        @if($type->is_mandatory)<span class="text-[10px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">WAJIB</span>@endif
                        <span class="text-xs text-slate-400">{{ $type->plans_count }} plan</span>
                    </div>
                </x-ui.card>
            @empty
                <div class="col-span-full text-center py-8 text-slate-400 text-sm">Belum ada tipe benefit.</div>
            @endforelse
        </div>
    @endif

    {{-- Type Form Modal --}}
    @if($showTypeForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showTypeForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingTypeId ? 'Edit' : 'Tambah' }} Tipe Benefit</h3></div>
                <form wire:submit="saveType" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama <span class="text-danger">*</span></label><input type="text" wire:model="typeName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('typeName')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kategori</label><select wire:model="typeCategory" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="insurance">🛡️ Asuransi</option><option value="bpjs">🏥 BPJS</option><option value="allowance">💰 Tunjangan</option><option value="facility">🏢 Fasilitas</option><option value="other">📋 Lainnya</option></select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi</label><textarea wire:model="typeDescription" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="typeIsMandatory" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">Benefit Wajib</span></label>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showTypeForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif

    {{-- Plan Form Modal --}}
    @if($showPlanForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showPlanForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingPlanId ? 'Edit' : 'Tambah' }} Plan</h3></div>
                <form wire:submit="savePlan" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tipe Benefit <span class="text-danger">*</span></label><select wire:model="planTypeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih tipe...</option>@foreach($this->types as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama Plan <span class="text-danger">*</span></label><input type="text" wire:model="planName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Provider</label><input type="text" wire:model="planProvider" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Coverage</label><input type="number" wire:model="planCoverage" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" step="0.01"></div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showPlanForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif

    {{-- Enrollment Form Modal --}}
    @if($showEnrollForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showEnrollForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[540px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Daftarkan Benefit</h3></div>
                <form wire:submit="saveEnroll" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label><select wire:model="enrollEmployeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih Karyawan...</option>@foreach($this->employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Plan <span class="text-danger">*</span></label><select wire:model="enrollPlanId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih Plan...</option>@foreach($this->plans as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->benefitType->name }})</option>@endforeach</select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal Efektif <span class="text-danger">*</span></label><input type="date" wire:model="enrollEffectiveDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kontribusi Karyawan</label><input type="number" wire:model="enrollEmployeeContrib" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" step="0.01"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kontribusi Perusahaan</label><input type="number" wire:model="enrollEmployerContrib" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" step="0.01"></div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showEnrollForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
