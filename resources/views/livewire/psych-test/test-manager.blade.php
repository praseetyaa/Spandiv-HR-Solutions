<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Tes Psikologi</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola bank tes psikologi dan pertanyaan</p>
        </div>
        <button wire:click="openTestForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Tes
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php $st = $this->stats; @endphp
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-slate-700">{{ $st['total'] }}</div><div class="text-xs font-medium text-slate-400 mt-1">Total Tes</div></div></x-ui.card>
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-emerald-600">{{ $st['active'] }}</div><div class="text-xs font-medium text-emerald-500 mt-1">Aktif</div></div></x-ui.card>
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-amber-600">{{ $st['draft'] }}</div><div class="text-xs font-medium text-amber-500 mt-1">Draft</div></div></x-ui.card>
        <x-ui.card><div class="text-center"><div class="text-2xl font-extrabold text-purple-600">{{ $st['questions'] }}</div><div class="text-xs font-medium text-purple-500 mt-1">Total Soal</div></div></x-ui.card>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tes..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        </div>
        <select wire:model.live="categoryFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Kategori</option>
            <option value="personality">🧠 Personality</option>
            <option value="intelligence">💡 Intelligence</option>
            <option value="arithmetic">🔢 Arithmetic</option>
            <option value="sjt">🤝 SJT</option>
            <option value="projective">🎨 Projective</option>
        </select>
        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="draft">📝 Draft</option>
            <option value="active">🟢 Aktif</option>
            <option value="inactive">⚪ Nonaktif</option>
        </select>
    </div>

    {{-- Split: Test list + Question detail --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Test Grid --}}
        <div class="lg:col-span-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($this->tests as $test)
                    @php
                        $statusBadge = match($test->status) {
                            'draft'    => ['Draft', 'bg-slate-100 text-slate-500'],
                            'active'   => ['Aktif', 'bg-emerald-50 text-emerald-600'],
                            'inactive' => ['Nonaktif', 'bg-red-50 text-red-500'],
                            default    => [$test->status, 'bg-slate-100 text-slate-500'],
                        };
                    @endphp
                    <div wire:click="selectTest({{ $test->id }})" class="cursor-pointer transition-all duration-200 {{ $selectedTestId === $test->id ? 'ring-2 ring-brand' : '' }}">
                        <x-ui.card>
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0 mr-2">
                                    <h4 class="m-0 text-sm font-bold text-slate-900">{{ $test->name }}</h4>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $test->code }}</div>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $statusBadge[1] }} shrink-0">{{ $statusBadge[0] }}</span>
                            </div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs bg-purple-50 text-purple-600 px-2 py-0.5 rounded-full font-medium">{{ $test->category_label }}</span>
                                <span class="text-xs text-slate-400">⏱️ {{ $test->duration_minutes }}m</span>
                            </div>
                            @if($test->description)
                                <p class="text-xs text-slate-500 m-0 mb-3 line-clamp-2">{{ $test->description }}</p>
                            @endif
                            <div class="grid grid-cols-3 gap-2 mb-3">
                                <div class="text-center p-1.5 bg-slate-50 rounded-lg"><div class="text-xs font-bold text-slate-700">{{ $test->questions_count }}</div><div class="text-[10px] text-slate-400">Soal</div></div>
                                <div class="text-center p-1.5 bg-slate-50 rounded-lg"><div class="text-xs font-bold text-slate-700">{{ $test->sections_count }}</div><div class="text-[10px] text-slate-400">Seksi</div></div>
                                <div class="text-center p-1.5 bg-slate-50 rounded-lg"><div class="text-xs font-bold text-slate-700">{{ $test->assignments_count }}</div><div class="text-[10px] text-slate-400">Assigned</div></div>
                            </div>
                            <div class="flex items-center justify-end gap-1 pt-2 border-t border-slate-100">
                                @if($test->status === 'draft')
                                    <button wire:click.stop="updateTestStatus({{ $test->id }}, 'active')" class="p-1.5 rounded-lg border-none bg-emerald-50 text-emerald-500 cursor-pointer hover:bg-emerald-100 transition-colors text-[11px] font-semibold px-2">Aktifkan</button>
                                @elseif($test->status === 'active')
                                    <button wire:click.stop="updateTestStatus({{ $test->id }}, 'inactive')" class="p-1.5 rounded-lg border-none bg-amber-50 text-amber-500 cursor-pointer hover:bg-amber-100 transition-colors text-[11px] font-semibold px-2">Nonaktifkan</button>
                                @endif
                                <button wire:click.stop="openTestForm({{ $test->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                <button wire:click.stop="deleteTest({{ $test->id }})" wire:confirm="Hapus tes ini?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                            </div>
                        </x-ui.card>
                    </div>
                @empty
                    <div class="col-span-full"><x-ui.card><div class="text-center py-12 text-slate-400"><div class="text-3xl mb-2">🧠</div><div class="font-medium text-slate-600 mb-1">Belum Ada Tes</div><div class="text-sm">Buat tes psikologi pertama Anda.</div></div></x-ui.card></div>
                @endforelse
            </div>
        </div>

        {{-- Question Panel --}}
        <div class="lg:col-span-2">
            <x-ui.card>
                @if($this->selectedTest)
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="m-0 text-lg font-bold text-slate-900">{{ $this->selectedTest->name }}</h3>
                            <span class="text-xs text-slate-400">{{ $this->selectedTest->questions->count() }} soal</span>
                        </div>
                        <button wire:click="openQuestionForm" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand text-white text-xs font-semibold rounded-lg border-none cursor-pointer hover:bg-brand-600 transition-colors">+ Soal</button>
                    </div>

                    @forelse($this->selectedTest->sections as $section)
                        <div class="mb-4">
                            <div class="text-xs font-bold text-slate-500 uppercase mb-2 flex items-center gap-2">
                                <span class="w-5 h-5 bg-brand-100 text-brand-700 rounded flex items-center justify-center text-[10px] font-bold">{{ $section->order_number }}</span>
                                {{ $section->name }}
                                <span class="text-slate-300 font-normal">({{ $section->question_type }})</span>
                            </div>
                            @foreach($section->questions as $q)
                                <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 mb-1.5 group">
                                    <span class="text-[10px] font-bold text-slate-400 mt-0.5 w-5 shrink-0">{{ $q->order_number }}.</span>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs text-slate-700 line-clamp-2">{{ $q->content }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $q->type }} · {{ $q->points }} pts</div>
                                    </div>
                                    <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                                        <button wire:click="openQuestionForm({{ $q->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                        <button wire:click="deleteQuestion({{ $q->id }})" wire:confirm="Hapus soal?" class="p-1 rounded border-none bg-transparent text-red-400 cursor-pointer hover:text-red-600"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        {{-- Questions without sections --}}
                        @forelse($this->selectedTest->questions->whereNull('section_id') as $q)
                            <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 mb-1.5 group">
                                <span class="text-[10px] font-bold text-slate-400 mt-0.5 w-5 shrink-0">{{ $q->order_number }}.</span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs text-slate-700 line-clamp-2">{{ $q->content }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $q->type }} · {{ $q->points }} pts</div>
                                </div>
                                <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                                    <button wire:click="openQuestionForm({{ $q->id }})" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-slate-600"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                    <button wire:click="deleteQuestion({{ $q->id }})" wire:confirm="Hapus soal?" class="p-1 rounded border-none bg-transparent text-red-400 cursor-pointer hover:text-red-600"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-sm">Belum ada soal.</div>
                        @endforelse
                    @endforelse
                @else
                    <div class="text-center py-12"><div class="text-3xl mb-2">👆</div><div class="text-sm text-slate-400">Pilih tes untuk melihat soal</div></div>
                @endif
            </x-ui.card>
        </div>
    </div>

    {{-- Test Form Modal --}}
    @if($showTestForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showTestForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[600px] max-h-[85vh] overflow-y-auto">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingTestId ? 'Edit' : 'Tambah' }} Tes Psikologi</h3></div>
                <form wire:submit="saveTest" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Nama Tes <span class="text-danger">*</span></label><input type="text" wire:model="testName" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('testName')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kode <span class="text-danger">*</span></label><input type="text" wire:model="testCode" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="e.g. DISC-01">@error('testCode')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Kategori</label><select wire:model="testCategory" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="personality">🧠 Personality</option><option value="intelligence">💡 Intelligence</option><option value="arithmetic">🔢 Arithmetic</option><option value="sjt">🤝 SJT</option><option value="projective">🎨 Projective</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Durasi (menit)</label><input type="number" wire:model="testDuration" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi</label><textarea wire:model="testDescription" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Instruksi <span class="text-danger">*</span></label><textarea wire:model="testInstructions" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea>@error('testInstructions')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Passing Score</label><input type="number" step="0.01" wire:model="testPassingScore" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div class="flex flex-col gap-2 justify-center">
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="testRandomizeQ" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700">Acak Soal</span></label>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="testRandomizeOpt" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700">Acak Pilihan</span></label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showTestForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif

    {{-- Question Form Modal --}}
    @if($showQuestionForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showQuestionForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[600px] max-h-[85vh] overflow-y-auto">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingQuestionId ? 'Edit' : 'Tambah' }} Soal</h3></div>
                <form wire:submit="saveQuestion" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-3 gap-3">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tipe</label><select wire:model="questionType" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="multiple_choice">Multiple Choice</option><option value="true_false">True/False</option><option value="essay">Essay</option><option value="number_series">Number Series</option><option value="number_input">Number Input</option></select></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Urutan</label><input type="number" wire:model="questionOrder" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Poin</label><input type="number" step="0.01" wire:model="questionPoints" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Pertanyaan <span class="text-danger">*</span></label><textarea wire:model="questionContent" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea>@error('questionContent')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Dimension Key</label><input type="text" wire:model="questionDimensionKey" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none" placeholder="e.g. openness, agreeableness"></div>

                    {{-- Options --}}
                    @if(in_array($questionType, ['multiple_choice', 'true_false']))
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-[13px] font-semibold text-slate-700">Pilihan Jawaban</label>
                                <button type="button" wire:click="addOption" class="text-xs text-brand font-semibold cursor-pointer bg-transparent border-none hover:underline">+ Tambah Pilihan</button>
                            </div>
                            <div class="space-y-2">
                                @foreach($questionOptions as $idx => $opt)
                                    <div class="flex items-center gap-2 p-2.5 rounded-lg bg-slate-50">
                                        <input type="checkbox" wire:model="questionOptions.{{ $idx }}.is_correct" class="accent-emerald-500 w-4 h-4" title="Jawaban benar">
                                        <input type="text" wire:model="questionOptions.{{ $idx }}.content" class="form-input flex-1 py-1.5 px-2.5 border border-slate-200 rounded-lg text-sm outline-none" placeholder="Pilihan...">
                                        <input type="number" step="0.01" wire:model="questionOptions.{{ $idx }}.score_value" class="form-input w-16 py-1.5 px-2 border border-slate-200 rounded-lg text-sm outline-none text-center" placeholder="Skor">
                                        @if(count($questionOptions) > 2)
                                            <button type="button" wire:click="removeOption({{ $idx }})" class="p-1 border-none bg-transparent text-red-400 cursor-pointer hover:text-red-600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showQuestionForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
