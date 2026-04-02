<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Training Program</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola program pelatihan dan jadwal sesi</p>
        </div>
        <button wire:click="openProgramForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Program
        </button>
    </div>

    {{-- Search --}}
    <div class="mb-6">
        <div class="relative max-w-[320px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari program..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        </div>
    </div>

    {{-- Programs List --}}
    <div class="flex flex-col gap-4">
        @forelse($this->programs as $program)
            <x-ui.card>
                {{-- Program Header --}}
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-xl shrink-0">🎓</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-base font-bold text-slate-900">{{ $program->name }}</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-teal-50 text-teal-700 rounded-full uppercase">{{ $program->category }}</span>
                        </div>
                        <div class="text-sm text-slate-500">Max {{ $program->max_participants }} peserta · {{ $program->schedules->count() }} jadwal</div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button wire:click="openScheduleForm({{ $program->id }})" class="p-2 rounded-lg border-none bg-teal-50 text-teal-600 cursor-pointer hover:bg-teal-100 transition-colors" title="Tambah Jadwal">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </button>
                        <button wire:click="openProgramForm({{ $program->id }})" class="p-2 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors" title="Edit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                        </button>
                        <button wire:click="toggleProgram({{ $program->id }})" class="p-2 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="transition-transform duration-200 {{ in_array($program->id, $expandedPrograms) ? 'rotate-180' : '' }}"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <button wire:click="deleteProgram({{ $program->id }})" wire:confirm="Hapus program ini?" class="p-2 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors" title="Hapus">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg>
                        </button>
                    </div>
                </div>

                @if($program->description)
                    <p class="mt-2 mb-0 text-sm text-slate-500">{{ Str::limit($program->description, 150) }}</p>
                @endif

                {{-- Expanded: Schedules --}}
                @if(in_array($program->id, $expandedPrograms))
                    <div class="mt-5 pt-5 border-t border-slate-100">
                        @forelse($program->schedules->sortByDesc('start_date') as $schedule)
                            <div class="p-4 rounded-xl bg-slate-50/70 mb-3 last:mb-0">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg">{{ $schedule->mode_icon }}</span>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">
                                                {{ $schedule->start_date->format('d M Y') }} — {{ $schedule->end_date->format('d M Y') }}
                                            </div>
                                            <div class="text-xs text-slate-400">
                                                {{ ucfirst($schedule->mode) }}
                                                @if($schedule->location) · {{ $schedule->location }} @endif
                                                @if($schedule->trainer_name) · Trainer: {{ $schedule->trainer_name }} @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        @php
                                            $seatColor = $schedule->remaining_seats > 5 ? 'text-green-600 bg-green-50' : ($schedule->remaining_seats > 0 ? 'text-amber-600 bg-amber-50' : 'text-red-600 bg-red-50');
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg {{ $seatColor }}">
                                            {{ $schedule->remaining_seats }}/{{ $schedule->available_seats }} kursi
                                        </span>
                                        <button wire:click="openParticipantForm({{ $schedule->id }})" class="p-1.5 rounded-lg border-none bg-teal-50 text-teal-600 cursor-pointer hover:bg-teal-100 transition-colors" title="Tambah Peserta">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                                        </button>
                                        <button wire:click="deleteSchedule({{ $schedule->id }})" wire:confirm="Hapus jadwal?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Participants --}}
                                @if($schedule->participants->count())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($schedule->participants as $p)
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white rounded-lg border border-slate-100 text-xs">
                                                <span class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center text-[9px] font-bold text-brand-700">{{ $p->employee->initials }}</span>
                                                <span class="text-slate-700">{{ $p->employee->full_name }}</span>
                                                @if($p->status === 'attended')
                                                    <span class="text-green-500">✓</span>
                                                @else
                                                    <button wire:click="markAttended({{ $p->id }})" class="text-slate-400 hover:text-green-500 cursor-pointer border-none bg-transparent p-0" title="Hadir">○</button>
                                                @endif
                                                <button wire:click="removeParticipant({{ $p->id }})" class="text-slate-300 hover:text-red-500 cursor-pointer border-none bg-transparent p-0">×</button>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="m-0 text-xs text-slate-400">Belum ada peserta terdaftar.</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-center py-6 text-sm text-slate-400 m-0">Belum ada jadwal. Klik ikon kalender untuk menambahkan.</p>
                        @endforelse
                    </div>
                @endif
            </x-ui.card>
        @empty
            <x-ui.card>
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 mx-auto mb-4 flex items-center justify-center text-2xl">🎓</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Program</h3>
                    <p class="text-sm text-slate-400 mb-4">Buat program training pertama.</p>
                    <button wire:click="openProgramForm" class="px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer">Buat Program</button>
                </div>
            </x-ui.card>
        @endforelse
    </div>

    {{-- Program Form Modal --}}
    @if($showProgramForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showProgramForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[540px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingProgramId ? 'Edit Program' : 'Buat Program' }}</h3></div>
                <form wire:submit="saveProgram" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama Program <span class="text-danger">*</span></label><input type="text" wire:model="programName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="Nama program">@error('programName') <p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p> @enderror</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kategori <span class="text-danger">*</span></label><input type="text" wire:model="programCategory" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="e.g. Safety"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Max Peserta</label><input type="number" wire:model="programMaxParticipants" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" min="1"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi</label><textarea wire:model="programDescription" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" wire:click="$set('showProgramForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Schedule Form Modal --}}
    @if($showScheduleForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showScheduleForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[540px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Tambah Jadwal</h3></div>
                <form wire:submit="saveSchedule" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Mulai <span class="text-danger">*</span></label><input type="date" wire:model="scheduleStartDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Selesai <span class="text-danger">*</span></label><input type="date" wire:model="scheduleEndDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Mode</label><select wire:model="scheduleMode" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="offline">🏢 Offline</option><option value="online">💻 Online</option><option value="hybrid">🔄 Hybrid</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kursi</label><input type="number" wire:model="scheduleSeats" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" min="1"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Lokasi</label><input type="text" wire:model="scheduleLocation" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="Ruang meeting / lokasi"></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Trainer</label><input type="text" wire:model="scheduleTrainerName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="Nama trainer"></div>
                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" wire:click="$set('showScheduleForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Participant Form Modal --}}
    @if($showParticipantForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showParticipantForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[420px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Daftarkan Peserta</h3></div>
                <form wire:submit="addParticipant" class="p-6 flex flex-col gap-4">
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label>
                        <select wire:model="participantEmployeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white">
                            <option value="">Pilih Karyawan...</option>
                            @foreach($this->employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" wire:click="$set('showParticipantForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Daftarkan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
