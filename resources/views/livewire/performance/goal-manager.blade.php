<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Goal / KPI</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola sasaran kerja dan KPI karyawan per siklus review</p>
        </div>
        <button wire:click="openGoalForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Goal
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php $st = $this->stats; @endphp
        <x-ui.card>
            <div class="text-center">
                <div class="text-2xl font-extrabold text-slate-700">{{ $st['total'] }}</div>
                <div class="text-xs font-medium text-slate-400 mt-1">Total Goal</div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-center">
                <div class="text-2xl font-extrabold text-emerald-600">{{ $st['active'] }}</div>
                <div class="text-xs font-medium text-emerald-500 mt-1">Aktif</div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-center">
                <div class="text-2xl font-extrabold text-blue-600">{{ $st['achieved'] }}</div>
                <div class="text-xs font-medium text-blue-500 mt-1">Tercapai</div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-center">
                <div class="text-2xl font-extrabold text-purple-600">{{ $st['avg_pct'] }}%</div>
                <div class="text-xs font-medium text-purple-500 mt-1">Rata-rata Capaian</div>
            </div>
        </x-ui.card>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari goal..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        </div>
        <select wire:model.live="cycleFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Siklus</option>
            @foreach($this->cycles as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="draft">📝 Draft</option>
            <option value="active">🟢 Aktif</option>
            <option value="achieved">✅ Tercapai</option>
            <option value="missed">❌ Tidak Tercapai</option>
        </select>
        <select wire:model.live="employeeFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Karyawan</option>
            @foreach($this->employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Goal Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($this->goals as $goal)
            @php
                $pct = $goal->achievement_percent;
                $pctClamped = min($pct, 100);
                $pctColor = $pct >= 100 ? 'bg-emerald-500' : ($pct >= 60 ? 'bg-blue-500' : ($pct >= 30 ? 'bg-amber-500' : 'bg-red-400'));
                $statusBadge = match($goal->status) {
                    'draft'    => ['Draft', 'default'],
                    'active'   => ['Aktif', 'success'],
                    'achieved' => ['Tercapai', 'success'],
                    'missed'   => ['Tidak Tercapai', 'danger'],
                    default    => [$goal->status, 'default'],
                };
            @endphp
            <x-ui.card>
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0 mr-2">
                        <h4 class="m-0 text-sm font-bold text-slate-900 truncate">{{ $goal->title }}</h4>
                        <div class="text-xs text-slate-400 mt-0.5 truncate">{{ $goal->employee?->full_name ?? '-' }}</div>
                    </div>
                    <x-ui.badge :type="$statusBadge[1]" size="xs">{{ $statusBadge[0] }}</x-ui.badge>
                </div>

                @if($goal->description)
                    <p class="text-xs text-slate-500 m-0 mb-3 line-clamp-2">{{ $goal->description }}</p>
                @endif

                {{-- Progress Bar --}}
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[11px] font-semibold text-slate-500">Progress</span>
                        <span class="text-[11px] font-bold {{ $pct >= 100 ? 'text-emerald-600' : 'text-slate-600' }}">{{ $pct }}%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $pctColor }} rounded-full transition-all duration-500" style="width: {{ $pctClamped }}%"></div>
                    </div>
                </div>

                {{-- Metrics --}}
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <div class="text-center p-2 bg-slate-50 rounded-lg">
                        <div class="text-xs font-bold text-slate-700">{{ number_format($goal->target, 0) }}</div>
                        <div class="text-[10px] text-slate-400">Target</div>
                    </div>
                    <div class="text-center p-2 bg-slate-50 rounded-lg">
                        <div class="text-xs font-bold text-brand">{{ number_format($goal->actual, 0) }}</div>
                        <div class="text-[10px] text-slate-400">Actual</div>
                    </div>
                    <div class="text-center p-2 bg-slate-50 rounded-lg">
                        <div class="text-xs font-bold text-purple-600">{{ $goal->weight_percent }}%</div>
                        <div class="text-[10px] text-slate-400">Bobot</div>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                    <span>{{ $goal->cycle?->name ?? '-' }}</span>
                    @if($goal->metric_unit)<span class="text-slate-300">{{ $goal->metric_unit }}</span>@endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-1 mt-3">
                    @if($goal->status === 'active')
                        <button wire:click="updateStatus({{ $goal->id }}, 'achieved')" class="p-1.5 rounded-lg border-none bg-emerald-50 text-emerald-500 cursor-pointer hover:bg-emerald-100 transition-colors text-[11px] font-semibold px-2">✅ Tercapai</button>
                    @endif
                    <button wire:click="openGoalForm({{ $goal->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                    <button wire:click="deleteGoal({{ $goal->id }})" wire:confirm="Hapus goal ini?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                </div>
            </x-ui.card>
        @empty
            <div class="col-span-full">
                <x-ui.card>
                    <div class="text-center py-12 text-slate-400">
                        <div class="text-3xl mb-2">🎯</div>
                        <div class="font-medium text-slate-600 mb-1">Belum Ada Goal</div>
                        <div class="text-sm">Tambahkan sasaran kerja pertama untuk karyawan Anda.</div>
                    </div>
                </x-ui.card>
            </div>
        @endforelse
    </div>

    {{-- Goal Form Modal --}}
    @if($showGoalForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showGoalForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[560px] max-h-[85vh] overflow-y-auto">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingGoalId ? 'Edit' : 'Tambah' }} Goal / KPI</h3></div>
                <form wire:submit="saveGoal" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label><select wire:model="goalEmployeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih Karyawan</option>@foreach($this->employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach</select>@error('goalEmployeeId')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Siklus <span class="text-danger">*</span></label><select wire:model="goalCycleId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih Siklus</option>@foreach($this->cycles as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>@error('goalCycleId')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Judul Goal <span class="text-danger">*</span></label><input type="text" wire:model="goalTitle" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="e.g. Increase monthly revenue">@error('goalTitle')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi</label><textarea wire:model="goalDescription" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div class="grid grid-cols-4 gap-3">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Target <span class="text-danger">*</span></label><input type="number" step="0.01" wire:model="goalTarget" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('goalTarget')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Actual</label><input type="number" step="0.01" wire:model="goalActual" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Bobot % <span class="text-danger">*</span></label><input type="number" wire:model="goalWeight" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="e.g. 20">@error('goalWeight')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Unit</label><input type="text" wire:model="goalMetricUnit" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="e.g. Rp"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Status</label><select wire:model="goalStatus" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="draft">Draft</option><option value="active">Aktif</option><option value="achieved">Tercapai</option><option value="missed">Tidak Tercapai</option></select></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showGoalForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
