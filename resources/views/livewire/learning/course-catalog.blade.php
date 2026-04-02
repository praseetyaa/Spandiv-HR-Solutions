<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Katalog Kursus</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola kursus online dan registrasi karyawan</p>
        </div>
        <button wire:click="openCourseForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Kursus
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative flex-1 min-w-[200px] max-w-[320px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kursus..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        </div>
        <select wire:model.live="categoryFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Kategori</option>
            @foreach($this->categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
        <select wire:model.live="levelFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Level</option>
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="advanced">Advanced</option>
        </select>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1fr_400px] gap-6">
        {{-- Course Grid --}}
        <div>
            @if($this->courses->count())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($this->courses as $course)
                        <button
                            wire:click="selectCourse({{ $course->id }})"
                            class="text-left p-5 rounded-xl border-2 bg-white transition-all duration-200 cursor-pointer hover:shadow-md
                                {{ $selectedCourseId === $course->id ? 'border-brand ring-2 ring-brand/20' : 'border-slate-100 hover:border-slate-200' }}"
                        >
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-bold text-slate-900 m-0 truncate">{{ $course->title }}</h3>
                                    <span class="text-xs text-slate-400 mt-0.5">{{ $course->category }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 ml-2 shrink-0">
                                    @if($course->is_mandatory)
                                        <span class="px-2 py-0.5 text-[10px] font-bold text-red-600 bg-red-50 rounded-full uppercase">Wajib</span>
                                    @endif
                                    @php
                                        $lvlColor = match($course->level) {
                                            'beginner' => 'bg-green-50 text-green-700',
                                            'intermediate' => 'bg-amber-50 text-amber-700',
                                            'advanced' => 'bg-purple-50 text-purple-700',
                                            default => 'bg-slate-50 text-slate-700',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase {{ $lvlColor }}">{{ ucfirst($course->level) }}</span>
                                </div>
                            </div>

                            @if($course->description)
                                <p class="text-xs text-slate-500 m-0 mb-3 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                            @endif

                            <div class="flex items-center gap-4 text-xs text-slate-400">
                                <span class="flex items-center gap-1">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    {{ $course->duration_hours }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    {{ $course->enrolled_count }} enrolled
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                    {{ $course->completed_count }} selesai
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            @else
                <x-ui.card>
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 mx-auto mb-4 flex items-center justify-center text-2xl">📚</div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Kursus</h3>
                        <p class="text-sm text-slate-400 mb-4">Buat kursus pertama untuk karyawan.</p>
                        <button wire:click="openCourseForm" class="px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg border-none cursor-pointer">Buat Kursus</button>
                    </div>
                </x-ui.card>
            @endif
        </div>

        {{-- Detail Panel --}}
        <div class="flex flex-col gap-4">
            @if($this->selectedCourse)
                @php $sc = $this->selectedCourse; @endphp
                <x-ui.card>
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="m-0 text-lg font-bold text-slate-900">{{ $sc->title }}</h3>
                            <span class="text-xs text-slate-400">{{ $sc->category }} · {{ $sc->duration_hours }}</span>
                        </div>
                        <div class="flex gap-1.5">
                            <button wire:click="openCourseForm({{ $sc->id }})" class="p-2 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors" title="Edit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                            </button>
                            <button wire:click="deleteCourse({{ $sc->id }})" wire:confirm="Hapus kursus ini?" class="p-2 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors" title="Hapus">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg>
                            </button>
                        </div>
                    </div>

                    @if($sc->description)
                        <p class="text-sm text-slate-600 mb-4">{{ $sc->description }}</p>
                    @endif

                    {{-- Sections --}}
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="m-0 text-sm font-semibold text-slate-700">Seksi Materi</h4>
                        <button wire:click="openSectionForm" class="text-xs font-medium text-brand hover:underline cursor-pointer bg-transparent border-none">+ Tambah Seksi</button>
                    </div>

                    @forelse($sc->sections as $section)
                        <div class="py-3 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-brand-50 text-brand text-xs font-bold flex items-center justify-center">{{ $section->order_number }}</span>
                                    <span class="text-sm font-medium text-slate-900">{{ $section->title }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-slate-400">{{ $section->duration_minutes }}m</span>
                                    <button wire:click="deleteSection({{ $section->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                </div>
                            </div>
                            @foreach($section->materials as $mat)
                                <div class="ml-8 mt-1.5 text-xs text-slate-500 flex items-center gap-1.5">
                                    <span>{{ $mat->type_icon }}</span>
                                    <span>{{ $mat->title }}</span>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4 m-0">Belum ada seksi materi.</p>
                    @endforelse
                </x-ui.card>

                {{-- Enrollments --}}
                <x-ui.card>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="m-0 text-sm font-semibold text-slate-700">Peserta ({{ $sc->enrollments->count() }})</h4>
                        <button wire:click="openEnrollForm" class="text-xs font-medium text-brand hover:underline cursor-pointer bg-transparent border-none">+ Daftarkan</button>
                    </div>

                    @forelse($sc->enrollments as $enroll)
                        <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-slate-50' : '' }}">
                            <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-700">
                                {{ $enroll->employee->initials }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-slate-900 truncate">{{ $enroll->employee->full_name }}</div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-brand rounded-full" style="width: {{ $enroll->progress_percent }}%"></div>
                                </div>
                                <span class="text-xs text-slate-400 w-8 text-right">{{ $enroll->progress_percent }}%</span>
                                <button wire:click="removeEnrollment({{ $enroll->id }})" wire:confirm="Hapus enrollment?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4 m-0">Belum ada peserta.</p>
                    @endforelse
                </x-ui.card>
            @else
                <x-ui.card>
                    <div class="text-center py-12 text-slate-400">
                        <div class="text-4xl mb-3">👈</div>
                        <p class="text-sm m-0">Pilih kursus untuk melihat detail</p>
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>

    {{-- Course Form Modal --}}
    @if($showCourseForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showCourseForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[540px]">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingCourseId ? 'Edit Kursus' : 'Tambah Kursus' }}</h3>
                    <button wire:click="$set('showCourseForm', false)" class="p-1.5 border-none bg-slate-100 rounded-lg cursor-pointer text-slate-500 hover:bg-slate-200"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                </div>
                <form wire:submit="saveCourse" class="p-6 flex flex-col gap-4">
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Judul <span class="text-danger">*</span></label>
                        <input type="text" wire:model="courseTitle" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="Nama kursus">
                        @error('courseTitle') <p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kategori <span class="text-danger">*</span></label>
                            <input type="text" wire:model="courseCategory" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="e.g. Leadership">
                        </div>
                        <div>
                            <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Level</label>
                            <select wire:model="courseLevel" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Durasi (menit)</label>
                            <input type="number" wire:model="courseDuration" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" min="1">
                        </div>
                        <div class="flex items-end pb-1">
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 font-medium">
                                <input type="checkbox" wire:model="courseIsMandatory" class="accent-brand w-4 h-4 cursor-pointer">
                                Kursus Wajib
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi</label>
                        <textarea wire:model="courseDescription" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]" placeholder="Deskripsi kursus..."></textarea>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" wire:click="$set('showCourseForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Section Form Modal --}}
    @if($showSectionForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showSectionForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[420px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Tambah Seksi</h3></div>
                <form wire:submit="saveSection" class="p-6 flex flex-col gap-4">
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Judul Seksi <span class="text-danger">*</span></label>
                        <input type="text" wire:model="sectionTitle" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Urutan</label><input type="number" wire:model="sectionOrder" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" min="1"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Durasi (menit)</label><input type="number" wire:model="sectionDuration" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" min="1"></div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" wire:click="$set('showSectionForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Enroll Form Modal --}}
    @if($showEnrollForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showEnrollForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[420px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Daftarkan Karyawan</h3></div>
                <form wire:submit="enrollEmployee" class="p-6 flex flex-col gap-4">
                    <div>
                        <label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Karyawan <span class="text-danger">*</span></label>
                        <select wire:model="enrollEmployeeId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white">
                            <option value="">Pilih Karyawan...</option>
                            @foreach($this->employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" wire:click="$set('showEnrollForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Daftarkan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
