<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Individual Development Plans</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola rencana pengembangan individu karyawan</p>
        </div>
        <button wire:click="openPlanForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat IDP Baru
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <select wire:model.live="employeeFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Karyawan</option>
            @foreach($this->employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
            @endforeach
        </select>

        <select wire:model.live="yearFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Tahun</option>
            @for($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>

        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
        </select>
    </div>

    {{-- IDP Plans List --}}
    <div class="flex flex-col gap-4">
        @forelse($this->plans as $plan)
            <x-ui.card>
                {{-- Plan Header --}}
                <div class="flex items-center gap-4">
                    {{-- Employee Avatar --}}
                    <div class="w-12 h-12 rounded-full bg-brand-100 flex items-center justify-center text-sm font-bold text-brand-700 shrink-0">
                        {{ $plan->employee->initials }}
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-base font-bold text-slate-900">{{ $plan->employee->full_name }}</span>
                            @php
                                $statusType = match($plan->status) {
                                    'draft' => 'default',
                                    'active' => 'success',
                                    'completed' => 'info',
                                    default => 'default',
                                };
                            @endphp
                            <x-ui.badge :type="$statusType" size="xs">{{ ucfirst($plan->status) }}</x-ui.badge>
                        </div>
                        <div class="text-sm text-slate-500">
                            {{ $plan->employee->jobPosition?->name ?? '-' }}
                            · Tahun {{ $plan->year }}
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="text-right shrink-0 hidden sm:block">
                        <div class="text-xl font-extrabold text-slate-900">{{ $plan->progress_percentage }}%</div>
                        <div class="text-xs text-slate-400">Progress</div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1.5 shrink-0">
                        @if($plan->status === 'draft')
                            <button
                                wire:click="approvePlan({{ $plan->id }})"
                                class="p-2 rounded-lg border-none bg-green-50 text-green-600 cursor-pointer hover:bg-green-100 transition-colors"
                                title="Approve & Activate"
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                        @endif
                        <button
                            wire:click="openPlanForm({{ $plan->id }})"
                            class="p-2 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"
                            title="Edit"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                        </button>
                        <button
                            wire:click="togglePlan({{ $plan->id }})"
                            class="p-2 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"
                            title="Expand"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                class="transition-transform duration-200 {{ in_array($plan->id, $expandedPlans) ? 'rotate-180' : '' }}"
                            >
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>
                        <button
                            wire:click="deletePlan({{ $plan->id }})"
                            wire:confirm="Yakin hapus IDP plan ini?"
                            class="p-2 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"
                            title="Hapus"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-slate-500">{{ $plan->development_focus }}</span>
                        @php $summary = $plan->activity_summary; @endphp
                        <span class="text-xs text-slate-400">{{ $summary['completed'] }}/{{ $summary['total'] }} selesai</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all duration-500 {{ $plan->progress_percentage >= 100 ? 'bg-emerald-500' : 'bg-brand' }}"
                            style="width: {{ min($plan->progress_percentage, 100) }}%"
                        ></div>
                    </div>
                </div>

                {{-- Expanded: Activities --}}
                @if(in_array($plan->id, $expandedPlans))
                    <div class="mt-5 pt-5 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="m-0 text-sm font-semibold text-slate-700">Aktivitas Pengembangan</h4>
                            <button
                                wire:click="openActivityForm({{ $plan->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[13px] font-medium text-brand bg-brand-50 rounded-lg border-none cursor-pointer hover:bg-brand-100 transition-colors"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Tambah Aktivitas
                            </button>
                        </div>

                        @forelse($plan->activities->sortBy('target_date') as $activity)
                            <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-slate-50' : '' }}">
                                {{-- Icon --}}
                                <div class="w-9 h-9 rounded-lg bg-slate-50 flex items-center justify-center text-lg shrink-0">
                                    {{ $activity->activity_type_icon }}
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-sm font-semibold text-slate-900">{{ $activity->title }}</span>
                                        @php
                                            $actStatusType = match($activity->status) {
                                                'planned'     => 'default',
                                                'in_progress' => 'warning',
                                                'completed'   => 'success',
                                                'cancelled'   => 'danger',
                                                default       => 'default',
                                            };
                                        @endphp
                                        <x-ui.badge :type="$actStatusType" size="xs">{{ ucfirst(str_replace('_', ' ', $activity->status)) }}</x-ui.badge>
                                        @if($activity->is_overdue)
                                            <x-ui.badge type="danger" size="xs">Overdue</x-ui.badge>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        {{ ucfirst($activity->activity_type) }}
                                        · Target: {{ $activity->target_date->format('d M Y') }}
                                        @if($activity->completed_at)
                                            · Selesai: {{ $activity->completed_at->format('d M Y') }}
                                        @endif
                                    </div>
                                    @if($activity->description)
                                        <p class="mt-1 mb-0 text-xs text-slate-500">{{ Str::limit($activity->description, 120) }}</p>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($activity->status !== 'completed' && $activity->status !== 'cancelled')
                                        <button
                                            wire:click="markActivityComplete({{ $activity->id }})"
                                            class="p-1.5 rounded-lg border-none bg-green-50 text-green-600 cursor-pointer hover:bg-green-100 transition-colors"
                                            title="Tandai Selesai"
                                        >
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        </button>
                                    @endif
                                    <button
                                        wire:click="openActivityForm({{ $plan->id }}, {{ $activity->id }})"
                                        class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"
                                        title="Edit"
                                    >
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                    </button>
                                    <button
                                        wire:click="deleteActivity({{ $activity->id }})"
                                        wire:confirm="Yakin hapus aktivitas ini?"
                                        class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"
                                        title="Hapus"
                                    >
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-sm">
                                <p class="m-0">Belum ada aktivitas. Klik tombol di atas untuk menambahkan.</p>
                            </div>
                        @endforelse
                    </div>
                @endif
            </x-ui.card>
        @empty
            <x-ui.card>
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 mx-auto mb-4 flex items-center justify-center">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada IDP</h3>
                    <p class="text-sm text-slate-400 mb-4">Buat rencana pengembangan pertama untuk karyawan Anda.</p>
                    <button wire:click="openPlanForm" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">
                        Buat IDP Baru
                    </button>
                </div>
            </x-ui.card>
        @endforelse
    </div>

    {{-- Plan Form Modal --}}
    @if($showPlanForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showPlanForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[540px]">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="m-0 text-[17px] font-semibold text-slate-900">
                        {{ $editingPlanId ? 'Edit IDP Plan' : 'Buat IDP Plan Baru' }}
                    </h3>
                    <button wire:click="$set('showPlanForm', false)" class="p-1.5 border-none bg-slate-100 rounded-lg cursor-pointer text-slate-500 hover:bg-slate-200">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <form wire:submit="savePlan" class="p-6 flex flex-col gap-5">
                    {{-- Employee --}}
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label>
                        <select wire:model="planEmployeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white" {{ $editingPlanId ? 'disabled' : '' }}>
                            <option value="">Pilih Karyawan...</option>
                            @foreach($this->employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                        @error('planEmployeeId') <p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Year --}}
                        <div>
                            <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tahun <span class="text-danger">*</span></label>
                            <select wire:model="planYear" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white">
                                @for($y = now()->year + 1; $y >= now()->year - 3; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                            @error('planYear') <p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Status</label>
                            <select wire:model="planStatus" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white">
                                <option value="draft">Draft</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>

                    {{-- Development Focus --}}
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Fokus Pengembangan <span class="text-danger">*</span></label>
                        <textarea
                            wire:model="planFocus"
                            rows="3"
                            class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"
                            placeholder="Apa yang ingin dikembangkan?"
                        ></textarea>
                        @error('planFocus') <p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" wire:click="$set('showPlanForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                            {{ $editingPlanId ? 'Simpan Perubahan' : 'Buat IDP' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Activity Form Modal --}}
    @if($showActivityForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showActivityForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[540px]">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="m-0 text-[17px] font-semibold text-slate-900">
                        {{ $editingActivityId ? 'Edit Aktivitas' : 'Tambah Aktivitas' }}
                    </h3>
                    <button wire:click="$set('showActivityForm', false)" class="p-1.5 border-none bg-slate-100 rounded-lg cursor-pointer text-slate-500 hover:bg-slate-200">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <form wire:submit="saveActivity" class="p-6 flex flex-col gap-5">
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Type --}}
                        <div>
                            <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Jenis Aktivitas <span class="text-danger">*</span></label>
                            <select wire:model="activityType" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white">
                                <option value="training">🎓 Training</option>
                                <option value="mentoring">🤝 Mentoring</option>
                                <option value="project">📋 Project</option>
                                <option value="course">📚 Course</option>
                                <option value="certification">🏅 Certification</option>
                                <option value="other">📌 Lainnya</option>
                            </select>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Status</label>
                            <select wire:model="activityStatus" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white">
                                <option value="planned">Planned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Judul <span class="text-danger">*</span></label>
                        <input type="text" wire:model="activityTitle" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="Nama aktivitas...">
                        @error('activityTitle') <p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi</label>
                        <textarea wire:model="activityDescription" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]" placeholder="Detail aktivitas..."></textarea>
                    </div>

                    {{-- Target Date --}}
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Target Selesai <span class="text-danger">*</span></label>
                        <input type="date" wire:model="activityTargetDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">
                        @error('activityTargetDate') <p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>

                    {{-- Outcome --}}
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Outcome / Hasil</label>
                        <textarea wire:model="activityOutcome" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]" placeholder="Apa hasil yang diharapkan/dicapai?"></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" wire:click="$set('showActivityForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                            {{ $editingActivityId ? 'Simpan Perubahan' : 'Tambah Aktivitas' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
