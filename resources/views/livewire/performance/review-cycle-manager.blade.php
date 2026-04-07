<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Siklus Review</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola siklus penilaian performa karyawan</p>
        </div>
        <button wire:click="openCycleForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Siklus
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php $st = $this->stats; @endphp
        <x-ui.card>
            <div class="text-center">
                <div class="text-2xl font-extrabold text-slate-700">{{ $st['total'] }}</div>
                <div class="text-xs font-medium text-slate-400 mt-1">Total Siklus</div>
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
                <div class="text-2xl font-extrabold text-blue-600">{{ $st['completed'] }}</div>
                <div class="text-xs font-medium text-blue-500 mt-1">Selesai</div>
            </div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-center">
                <div class="text-2xl font-extrabold text-purple-600">{{ $st['reviews'] }}</div>
                <div class="text-xs font-medium text-purple-500 mt-1">Total Review</div>
            </div>
        </x-ui.card>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari siklus..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        </div>
        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="draft">📝 Draft</option>
            <option value="active">🟢 Aktif</option>
            <option value="completed">✅ Selesai</option>
        </select>
    </div>

    {{-- Split: Cycles list + Reviews --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Cycle List --}}
        <div class="lg:col-span-3">
            <x-ui.card :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Siklus</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Periode</th>
                            <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Tipe</th>
                            <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Review</th>
                            <th class="text-center py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Status</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-500 text-xs uppercase">Aksi</th>
                        </tr></thead>
                        <tbody>
                            @forelse($this->cycles as $cycle)
                                <tr wire:click="selectCycle({{ $cycle->id }})" class="border-b border-slate-50 hover:bg-slate-50/50 cursor-pointer transition-colors {{ $selectedCycleId === $cycle->id ? 'bg-brand-50' : '' }}">
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-slate-900">{{ $cycle->name }}</div>
                                        @if($cycle->is_360)<span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded">360°</span>@endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 text-xs">
                                        {{ $cycle->start_date?->format('d M Y') }} — {{ $cycle->end_date?->format('d M Y') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @php
                                            $typeBadge = match($cycle->type) {
                                                'annual'    => ['🗓️ Tahunan', 'bg-blue-50 text-blue-600'],
                                                'mid-year'  => ['📅 Mid-Year', 'bg-teal-50 text-teal-600'],
                                                'quarterly' => ['📊 Quarterly', 'bg-amber-50 text-amber-600'],
                                                'probation' => ['⏱️ Probation', 'bg-orange-50 text-orange-600'],
                                                default     => [$cycle->type, 'bg-slate-50 text-slate-600'],
                                            };
                                        @endphp
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $typeBadge[1] }}">{{ $typeBadge[0] }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 text-xs font-bold text-slate-600">{{ $cycle->reviews_count }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @php
                                            $statusBadge = match($cycle->status) {
                                                'draft'     => ['Draft', 'bg-slate-100 text-slate-500'],
                                                'active'    => ['Aktif', 'bg-emerald-50 text-emerald-600'],
                                                'completed' => ['Selesai', 'bg-blue-50 text-blue-600'],
                                                default     => [$cycle->status, 'bg-slate-100 text-slate-500'],
                                            };
                                        @endphp
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $statusBadge[1] }}">{{ $statusBadge[0] }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            @if($cycle->status === 'draft')
                                                <button wire:click.stop="updateCycleStatus({{ $cycle->id }}, 'active')" class="p-1.5 rounded-lg border-none bg-emerald-50 text-emerald-500 cursor-pointer hover:bg-emerald-100 transition-colors" title="Aktifkan"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></button>
                                            @elseif($cycle->status === 'active')
                                                <button wire:click.stop="updateCycleStatus({{ $cycle->id }}, 'completed')" class="p-1.5 rounded-lg border-none bg-blue-50 text-blue-500 cursor-pointer hover:bg-blue-100 transition-colors" title="Selesaikan"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></button>
                                            @endif
                                            <button wire:click.stop="openCycleForm({{ $cycle->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                            <button wire:click.stop="deleteCycle({{ $cycle->id }})" wire:confirm="Hapus siklus ini?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">📋</div>
                                    <div class="font-medium text-slate-600 mb-1">Belum Ada Siklus Review</div>
                                    <div class="text-sm">Buat siklus pertama untuk mulai menilai performa.</div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>

        {{-- Detail Panel: Reviews --}}
        <div class="lg:col-span-2">
            <x-ui.card>
                @if($this->selectedCycle)
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="m-0 text-lg font-bold text-slate-900">{{ $this->selectedCycle->name }}</h3>
                            <span class="text-xs text-slate-400">{{ $this->selectedCycle->reviews->count() }} review</span>
                        </div>
                        <button wire:click="openReviewForm" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand text-white text-xs font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Review</button>
                    </div>

                    <div class="space-y-2">
                        @forelse($this->selectedCycle->reviews as $review)
                            <div class="flex items-center gap-3 p-3 rounded-xl {{ $review->status === 'submitted' ? 'bg-emerald-50' : 'bg-slate-50' }} transition-colors">
                                <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-sm font-bold text-brand-700 shrink-0">
                                    {{ substr($review->employee?->first_name ?? '?', 0, 1) }}{{ substr($review->employee?->last_name ?? '', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-slate-900 truncate">{{ $review->employee?->full_name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-slate-400 truncate">Reviewer: {{ $review->reviewer?->name ?? '-' }} ({{ $review->reviewer_type }})</div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    @if($review->final_score)
                                        <span class="text-sm font-bold text-brand">{{ number_format($review->final_score, 1) }}</span>
                                    @endif
                                    @if($review->rating)
                                        <x-ui.badge :type="$review->rating === 'exceeds' ? 'success' : ($review->rating === 'below' ? 'danger' : 'info')" size="xs">{{ ucfirst($review->rating) }}</x-ui.badge>
                                    @endif
                                    <div class="flex items-center gap-0.5">
                                        <button wire:click="openReviewForm({{ $review->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                        <button wire:click="deleteReview({{ $review->id }})" wire:confirm="Hapus review?" class="p-1 rounded border-none bg-transparent text-red-400 cursor-pointer hover:text-red-600"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-sm">
                                <p class="m-0">Belum ada review untuk siklus ini.</p>
                            </div>
                        @endforelse
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-3xl mb-2">👆</div>
                        <div class="text-sm text-slate-400">Pilih siklus untuk melihat review</div>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>

    {{-- Cycle Form Modal --}}
    @if($showCycleForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showCycleForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[520px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingCycleId ? 'Edit' : 'Tambah' }} Siklus Review</h3></div>
                <form wire:submit="saveCycle" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama Siklus <span class="text-danger">*</span></label><input type="text" wire:model="cycleName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="e.g. Review Tahunan 2026">@error('cycleName')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tipe</label><select wire:model="cycleType" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="annual">Tahunan</option><option value="mid-year">Mid-Year</option><option value="quarterly">Quarterly</option><option value="probation">Probation</option></select></div>
                        <div class="flex items-end pb-1"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="is360" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">360° Feedback</span></label></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal Mulai <span class="text-danger">*</span></label><input type="date" wire:model="startDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('startDate')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tanggal Selesai <span class="text-danger">*</span></label><input type="date" wire:model="endDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('endDate')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showCycleForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif

    {{-- Review Form Modal --}}
    @if($showReviewForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showReviewForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[560px] max-h-[85vh] overflow-y-auto">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingReviewId ? 'Edit' : 'Tambah' }} Performance Review</h3></div>
                <form wire:submit="saveReview" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label><select wire:model="reviewEmployeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih Karyawan</option>@foreach($this->employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach</select>@error('reviewEmployeeId')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Reviewer <span class="text-danger">*</span></label><select wire:model="reviewReviewerId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih Reviewer</option>@foreach($this->users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select>@error('reviewReviewerId')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tipe Reviewer</label><select wire:model="reviewerType" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="manager">Manager</option><option value="self">Self</option><option value="peer">Peer</option><option value="subordinate">Subordinate</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Rating</label><select wire:model="reviewRating" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">-</option><option value="exceeds">Exceeds</option><option value="meets">Meets</option><option value="below">Below</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Skor Akhir</label><input type="number" step="0.01" wire:model="reviewScore" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="0-100"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Ringkasan</label><textarea wire:model="reviewSummary" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Status</label><select wire:model="reviewStatus" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="draft">Draft</option><option value="in_progress">In Progress</option><option value="submitted">Submitted</option></select></div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showReviewForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
