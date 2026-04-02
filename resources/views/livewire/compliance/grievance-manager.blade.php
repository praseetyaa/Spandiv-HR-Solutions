<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Keluhan & Grievance</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola keluhan karyawan dan proses penyelesaian</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Keluhan
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-blue-100">
            <div class="text-2xl font-bold text-blue-600">{{ $this->stats['open'] }}</div>
            <div class="text-xs text-slate-400">Open</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-amber-100">
            <div class="text-2xl font-bold text-amber-600">{{ $this->stats['investigating'] }}</div>
            <div class="text-xs text-slate-400">Investigasi</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-emerald-100">
            <div class="text-2xl font-bold text-emerald-600">{{ $this->stats['resolved'] }}</div>
            <div class="text-xs text-slate-400">Selesai</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-red-100">
            <div class="text-2xl font-bold text-red-600">{{ $this->stats['critical'] }}</div>
            <div class="text-xs text-slate-400">Kritikal</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        </div>
        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="open">🟢 Open</option>
            <option value="investigating">🔍 Investigasi</option>
            <option value="resolved">✅ Selesai</option>
            <option value="closed">🔒 Ditutup</option>
        </select>
        <select wire:model.live="priorityFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Prioritas</option>
            <option value="low">Rendah</option>
            <option value="medium">Sedang</option>
            <option value="high">Tinggi</option>
            <option value="critical">Kritikal</option>
        </select>
    </div>

    {{-- Split layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- List --}}
        <div class="lg:col-span-3">
            <x-ui.card>
                <div class="divide-y divide-slate-50">
                    @forelse($this->grievances as $grievance)
                        <div wire:click="selectGrievance({{ $grievance->id }})" class="p-4 hover:bg-slate-50/50 cursor-pointer transition-colors {{ $selectedId === $grievance->id ? 'bg-brand-50' : '' }}">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    @php
                                        $priorityColors = ['low' => 'bg-slate-100 text-slate-600', 'medium' => 'bg-blue-100 text-blue-600', 'high' => 'bg-amber-100 text-amber-700', 'critical' => 'bg-red-100 text-red-700'];
                                        $statusColors = ['open' => 'bg-blue-100 text-blue-600', 'investigating' => 'bg-amber-100 text-amber-700', 'resolved' => 'bg-emerald-100 text-emerald-700', 'closed' => 'bg-slate-100 text-slate-600'];
                                    @endphp
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $priorityColors[$grievance->priority] ?? '' }}">{{ strtoupper($grievance->priority) }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $statusColors[$grievance->status] ?? '' }}">{{ strtoupper($grievance->status) }}</span>
                                </div>
                                <span class="text-xs text-slate-400">{{ $grievance->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-sm text-slate-700 mb-1.5 line-clamp-2">{{ $grievance->description }}</div>
                            <div class="flex items-center gap-3 text-xs text-slate-400">
                                <span>📁 {{ ucfirst($grievance->category) }}</span>
                                @if($grievance->is_anonymous)
                                    <span>🕶️ Anonim</span>
                                @elseif($grievance->employee)
                                    <span>👤 {{ $grievance->employee->full_name }}</span>
                                @endif
                                @if($grievance->assignedTo)
                                    <span>→ {{ $grievance->assignedTo->name }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-400">
                            <div class="text-3xl mb-2">😊</div>
                            <div class="font-medium text-slate-600 mb-1">Tidak Ada Keluhan</div>
                            <div class="text-sm">Semua baik-baik saja!</div>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>
        </div>

        {{-- Detail --}}
        <div class="lg:col-span-2">
            <x-ui.card>
                @if($this->selectedGrievance)
                    @php $g = $this->selectedGrievance; @endphp
                    <div class="p-1">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $priorityColors[$g->priority] ?? '' }}">{{ strtoupper($g->priority) }}</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $statusColors[$g->status] ?? '' }}">{{ strtoupper($g->status) }}</span>
                            </div>
                            <div class="flex gap-1">
                                <button wire:click="openForm({{ $g->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                <button wire:click="delete({{ $g->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                            </div>
                        </div>

                        <div class="text-sm text-slate-700 mb-4">{{ $g->description }}</div>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between"><span class="text-slate-400">Kategori</span><span class="font-medium text-slate-700">{{ ucfirst($g->category) }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-400">Pelapor</span><span class="font-medium text-slate-700">{{ $g->is_anonymous ? '🕶️ Anonim' : ($g->employee?->full_name ?? '-') }}</span></div>
                            @if($g->assignedTo)
                                <div class="flex justify-between"><span class="text-slate-400">Ditugaskan</span><span class="font-medium text-slate-700">{{ $g->assignedTo->name }}</span></div>
                            @endif
                            <div class="flex justify-between"><span class="text-slate-400">Dibuat</span><span class="font-medium text-slate-700">{{ $g->created_at->format('d M Y H:i') }}</span></div>
                        </div>

                        @if($g->resolution)
                            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 mb-4">
                                <div class="text-xs font-semibold text-emerald-700 mb-1">Resolusi</div>
                                <div class="text-sm text-emerald-800">{{ $g->resolution }}</div>
                                @if($g->resolved_at)<div class="text-xs text-emerald-500 mt-1">{{ $g->resolved_at->format('d M Y H:i') }}</div>@endif
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                            @if($g->status === 'open')
                                <button wire:click="updateStatus({{ $g->id }}, 'investigating')" class="px-3 py-1.5 text-xs font-semibold rounded-lg border-none bg-amber-100 text-amber-700 cursor-pointer hover:bg-amber-200 transition-colors">🔍 Investigasi</button>
                            @endif
                            @if(in_array($g->status, ['open', 'investigating']))
                                <button wire:click="openResolveForm({{ $g->id }})" class="px-3 py-1.5 text-xs font-semibold rounded-lg border-none bg-emerald-100 text-emerald-700 cursor-pointer hover:bg-emerald-200 transition-colors">✅ Selesaikan</button>
                            @endif
                            @if($g->status === 'resolved')
                                <button wire:click="updateStatus({{ $g->id }}, 'closed')" class="px-3 py-1.5 text-xs font-semibold rounded-lg border-none bg-slate-100 text-slate-700 cursor-pointer hover:bg-slate-200 transition-colors">🔒 Tutup</button>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-3xl mb-2">👆</div>
                        <div class="text-sm text-slate-400">Pilih keluhan untuk detail</div>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>

    {{-- Form Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[520px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Buat' }} Keluhan</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model.live="isAnonymous" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">Lapor secara anonim</span></label>
                    @if(!$isAnonymous)
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan</label><select wire:model="employeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih Karyawan...</option>@foreach($this->employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach</select></div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kategori</label><select wire:model="category" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="workplace">🏢 Lingkungan Kerja</option><option value="harassment">⚠️ Pelecehan</option><option value="discrimination">🚫 Diskriminasi</option><option value="management">👔 Manajemen</option><option value="safety">🦺 Keselamatan</option><option value="other">📋 Lainnya</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Prioritas</label><select wire:model="priority" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="low">Rendah</option><option value="medium">Sedang</option><option value="high">Tinggi</option><option value="critical">Kritikal</option></select></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi <span class="text-danger">*</span></label><textarea wire:model="description" rows="4" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea>@error('description')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif

    {{-- Resolve Modal --}}
    @if($showResolveForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showResolveForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Resolusi Keluhan</h3></div>
                <form wire:submit="resolveGrievance" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Resolusi <span class="text-danger">*</span></label><textarea wire:model="resolution" rows="5" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]" placeholder="Jelaskan langkah penyelesaian..."></textarea>@error('resolution')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showResolveForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-emerald-500 to-emerald-600 text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Selesaikan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
