<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">9-Box Talent Grid</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Visualisasi pemetaan potensi dan performa karyawan</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Department Filter --}}
            <select wire:model.live="departmentFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
                <option value="">Semua Departemen</option>
                @foreach($this->departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>

            {{-- Add Profile --}}
            <button wire:click="openProfileForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Profil
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1fr_360px] gap-6">
        {{-- 9-Box Grid --}}
        <x-ui.card>
            <div class="relative">
                {{-- Y-axis label --}}
                <div class="absolute -left-2 top-1/2 -translate-y-1/2 -rotate-90 text-xs font-semibold text-slate-400 uppercase tracking-widest whitespace-nowrap">
                    Potensi →
                </div>

                <div class="pl-8">
                    {{-- Grid rows (top row = high potential, bottom = low) --}}
                    @for($y = 2; $y >= 0; $y--)
                        <div class="flex items-stretch gap-2 mb-2">
                            {{-- Y-axis tick --}}
                            <div class="w-16 flex items-center justify-end pr-2 text-xs font-medium text-slate-500 shrink-0">
                                {{ $potentialLabels[$y] }}
                            </div>

                            @for($x = 0; $x <= 2; $x++)
                                @php
                                    $key = "{$x}-{$y}";
                                    $meta = $cellMeta[$key];
                                    $cellProfiles = $this->gridData[$key] ?? [];
                                    $count = count($cellProfiles);
                                    $isSelected = ($selectedX === $x && $selectedY === $y);

                                    $bgClasses = match($meta['color']) {
                                        'emerald' => 'bg-emerald-50 border-emerald-200 hover:bg-emerald-100',
                                        'teal'    => 'bg-teal-50 border-teal-200 hover:bg-teal-100',
                                        'purple'  => 'bg-purple-50 border-purple-200 hover:bg-purple-100',
                                        'sky'     => 'bg-sky-50 border-sky-200 hover:bg-sky-100',
                                        'slate'   => 'bg-slate-50 border-slate-200 hover:bg-slate-100',
                                        'orange'  => 'bg-orange-50 border-orange-200 hover:bg-orange-100',
                                        'blue'    => 'bg-blue-50 border-blue-200 hover:bg-blue-100',
                                        'amber'   => 'bg-amber-50 border-amber-200 hover:bg-amber-100',
                                        'red'     => 'bg-red-50 border-red-200 hover:bg-red-100',
                                        default   => 'bg-slate-50 border-slate-200',
                                    };

                                    $countColorClasses = match($meta['color']) {
                                        'emerald' => 'bg-emerald-500',
                                        'teal'    => 'bg-teal-500',
                                        'purple'  => 'bg-purple-500',
                                        'sky'     => 'bg-sky-500',
                                        'orange'  => 'bg-orange-500',
                                        'blue'    => 'bg-blue-500',
                                        'amber'   => 'bg-amber-500',
                                        'red'     => 'bg-red-500',
                                        default   => 'bg-slate-500',
                                    };
                                @endphp

                                <button
                                    wire:click="selectCell({{ $x }}, {{ $y }})"
                                    class="flex-1 min-h-[120px] rounded-xl border-2 p-4 text-left transition-all duration-200 cursor-pointer
                                        {{ $bgClasses }}
                                        {{ $isSelected ? 'ring-2 ring-brand ring-offset-2 scale-[1.02]' : '' }}"
                                >
                                    <div class="flex items-start justify-between mb-2">
                                        <span class="text-xs font-bold text-slate-600 uppercase">{{ $meta['label'] }}</span>
                                        @if($count > 0)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white text-xs font-bold {{ $countColorClasses }}">
                                                {{ $count }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 m-0 leading-relaxed">{{ $meta['desc'] }}</p>

                                    {{-- Mini avatars --}}
                                    @if($count > 0)
                                        <div class="flex -space-x-2 mt-3">
                                            @foreach(array_slice($cellProfiles, 0, 4) as $p)
                                                <div class="w-7 h-7 rounded-full bg-brand-200 border-2 border-white flex items-center justify-center text-[10px] font-bold text-brand-700" title="{{ $p->employee->full_name }}">
                                                    {{ $p->employee->initials }}
                                                </div>
                                            @endforeach
                                            @if($count > 4)
                                                <div class="w-7 h-7 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-[10px] font-bold text-slate-600">
                                                    +{{ $count - 4 }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </button>
                            @endfor
                        </div>
                    @endfor

                    {{-- X-axis labels --}}
                    <div class="flex ml-[72px] gap-2 mt-1">
                        @foreach($performanceLabels as $label)
                            <div class="flex-1 text-center text-xs font-medium text-slate-500">{{ $label }}</div>
                        @endforeach
                    </div>
                    <div class="text-center text-xs font-semibold text-slate-400 uppercase tracking-widest mt-2">
                        Performa →
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Detail Panel --}}
        <div class="flex flex-col gap-4">
            {{-- Summary Stats --}}
            <x-ui.card title="Ringkasan">
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center p-3 rounded-xl bg-emerald-50">
                        <div class="text-2xl font-extrabold text-emerald-700">
                            {{ count($this->gridData['2-2'] ?? []) }}
                        </div>
                        <div class="text-[11px] font-medium text-emerald-600">Stars ⭐</div>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-red-50">
                        <div class="text-2xl font-extrabold text-red-700">
                            {{ count($this->gridData['0-0'] ?? []) }}
                        </div>
                        <div class="text-[11px] font-medium text-red-600">At Risk ⚠️</div>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-purple-50">
                        <div class="text-2xl font-extrabold text-purple-700">
                            {{ $this->profiles->where('flight_risk', 'high')->count() }}
                        </div>
                        <div class="text-[11px] font-medium text-purple-600">Flight Risk</div>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-blue-50">
                        <div class="text-2xl font-extrabold text-blue-700">
                            {{ $this->profiles->count() }}
                        </div>
                        <div class="text-[11px] font-medium text-blue-600">Total Profil</div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Selected Cell Employees --}}
            @if($selectedX >= 0 && $selectedY >= 0)
                @php
                    $selKey = "{$selectedX}-{$selectedY}";
                    $selMeta = $cellMeta[$selKey];
                    $selProfiles = $this->gridData[$selKey] ?? [];
                @endphp
                <x-ui.card>
                    <x-slot:header>
                        <h3 class="m-0 text-base font-semibold text-slate-900">{{ $selMeta['label'] }}</h3>
                        <x-ui.badge type="info" size="xs">{{ count($selProfiles) }} karyawan</x-ui.badge>
                    </x-slot:header>

                    @forelse($selProfiles as $profile)
                        <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                            <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-sm font-bold text-brand-700 shrink-0">
                                {{ $profile->employee->initials }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-slate-900 truncate">{{ $profile->employee->full_name }}</div>
                                <div class="text-xs text-slate-400 truncate">{{ $profile->employee->jobPosition?->name ?? '-' }}</div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                @if($profile->flight_risk === 'high')
                                    <x-ui.badge type="danger" size="xs">🔥 Risk</x-ui.badge>
                                @endif
                                <button
                                    wire:click="openProfileForm({{ $profile->id }})"
                                    class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"
                                    title="Edit"
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-sm">
                            <p class="m-0">Belum ada karyawan di sel ini.</p>
                        </div>
                    @endforelse
                </x-ui.card>
            @endif
        </div>
    </div>

    {{-- Profile Form Modal --}}
    @if($showProfileForm)
        <div
            class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6"
            wire:click.self="closeProfileForm"
        >
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[540px] max-h-[85vh] overflow-y-auto">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="m-0 text-[17px] font-semibold text-slate-900">
                        {{ $editingProfileId ? 'Edit Talent Profile' : 'Tambah Talent Profile' }}
                    </h3>
                    <button wire:click="closeProfileForm" class="p-1.5 border-none bg-slate-100 rounded-lg cursor-pointer text-slate-500 hover:bg-slate-200">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <div class="p-6">
                    @livewire('talent.talent-profile-form', ['profileId' => $editingProfileId], key('profile-form-' . ($editingProfileId ?? 'new')))
                </div>
            </div>
        </div>
    @endif
</div>
