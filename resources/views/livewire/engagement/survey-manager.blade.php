<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">Pulse Survey</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Buat dan kelola survei untuk mengukur engagement karyawan</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Survey
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari survei..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        </div>
        <select wire:model.live="statusFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Status</option>
            <option value="draft">📝 Draft</option>
            <option value="active">🟢 Aktif</option>
            <option value="closed">🔒 Ditutup</option>
        </select>
    </div>

    {{-- Split layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Survey List --}}
        <div class="lg:col-span-3">
            <div class="grid grid-cols-1 gap-4">
                @forelse($this->surveys as $survey)
                    <x-ui.card>
                        <div wire:click="selectSurvey({{ $survey->id }})" class="cursor-pointer {{ $detailSurveyId === $survey->id ? 'ring-2 ring-brand-300 rounded-xl' : '' }}">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="m-0 text-[15px] font-bold text-slate-900">{{ $survey->title }}</h4>
                                    @if($survey->description)<p class="text-xs text-slate-400 mt-0.5 mb-0 line-clamp-1">{{ $survey->description }}</p>@endif
                                </div>
                                @php
                                    $statusBgs = ['draft' => 'bg-slate-100 text-slate-600', 'active' => 'bg-emerald-100 text-emerald-700', 'closed' => 'bg-blue-100 text-blue-600'];
                                @endphp
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full {{ $statusBgs[$survey->status] ?? '' }}">{{ strtoupper($survey->status) }}</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 mb-3">
                                <span>📅 {{ $survey->start_date->format('d M') }} - {{ $survey->end_date->format('d M Y') }}</span>
                                <span>❓ {{ $survey->questions_count }} pertanyaan</span>
                                <span>📝 {{ $survey->responses_count }} respon</span>
                                @if($survey->is_anonymous)<span>🕶️ Anonim</span>@endif
                            </div>
                            <div class="flex items-center gap-2">
                                @if($survey->status === 'draft')
                                    <button wire:click.stop="updateStatus({{ $survey->id }}, 'active')" class="px-3 py-1.5 text-xs font-semibold rounded-lg border-none bg-emerald-100 text-emerald-700 cursor-pointer hover:bg-emerald-200 transition-colors">🟢 Aktifkan</button>
                                @elseif($survey->status === 'active')
                                    <button wire:click.stop="updateStatus({{ $survey->id }}, 'closed')" class="px-3 py-1.5 text-xs font-semibold rounded-lg border-none bg-slate-100 text-slate-700 cursor-pointer hover:bg-slate-200 transition-colors">🔒 Tutup</button>
                                @endif
                                <button wire:click.stop="openQuestionForm({{ $survey->id }})" class="px-3 py-1.5 text-xs font-semibold rounded-lg border-none bg-brand-100 text-brand-700 cursor-pointer hover:bg-brand-200 transition-colors">+ Pertanyaan</button>
                                <button wire:click.stop="openForm({{ $survey->id }})" class="p-1.5 rounded-lg border-none bg-slate-100 text-slate-500 cursor-pointer hover:bg-slate-200 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg></button>
                                <button wire:click.stop="deleteSurvey({{ $survey->id }})" wire:confirm="Hapus?" class="p-1.5 rounded-lg border-none bg-red-50 text-red-500 cursor-pointer hover:bg-red-100 transition-colors"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                            </div>
                        </div>
                    </x-ui.card>
                @empty
                    <div class="text-center py-12 text-slate-400">
                        <div class="text-3xl mb-2">📊</div>
                        <div class="font-medium text-slate-600 mb-1">Belum Ada Survey</div>
                        <div class="text-sm">Buat pulse survey pertama Anda.</div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Detail: Questions --}}
        <div class="lg:col-span-2">
            <x-ui.card>
                @if($this->detailSurvey)
                    <div class="p-1">
                        <h3 class="m-0 text-lg font-bold text-slate-900 mb-1">{{ $this->detailSurvey->title }}</h3>
                        <div class="text-xs text-slate-400 mb-4">{{ $this->detailSurvey->questions->count() }} pertanyaan</div>

                        <div class="space-y-3">
                            @forelse($this->detailSurvey->questions as $question)
                                <div class="p-3 rounded-xl bg-slate-50">
                                    <div class="flex items-start justify-between mb-1">
                                        <span class="text-[10px] font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full">{{ $question->type_label }}</span>
                                        <button wire:click="deleteQuestion({{ $question->id }})" wire:confirm="Hapus pertanyaan?" class="p-1 rounded border-none bg-transparent text-slate-400 cursor-pointer hover:text-red-500"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg></button>
                                    </div>
                                    <div class="text-sm text-slate-700">{{ $question->content }}</div>
                                    @if($question->responses->count() > 0)
                                        <div class="mt-2 text-xs text-slate-400">{{ $question->responses->count() }} respon
                                            @if($question->type === 'rating' && $question->responses->avg('rating_value'))
                                                · Rata-rata: {{ number_format($question->responses->avg('rating_value'), 1) }} ⭐
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-sm text-slate-400 text-center py-4">Belum ada pertanyaan.</div>
                            @endforelse
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-3xl mb-2">📋</div>
                        <div class="text-sm text-slate-400">Pilih survey untuk melihat pertanyaan</div>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>

    {{-- Survey Form Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[520px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $editingId ? 'Edit' : 'Buat' }} Survey</h3></div>
                <form wire:submit="saveSurvey" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Judul <span class="text-danger">*</span></label><input type="text" wire:model="surveyTitle" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none">@error('surveyTitle')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Deskripsi</label><textarea wire:model="surveyDescription" rows="2" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Mulai <span class="text-danger">*</span></label><input type="date" wire:model="surveyStartDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Selesai <span class="text-danger">*</span></label><input type="date" wire:model="surveyEndDate" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none"></div>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="surveyAnonymous" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">Survei Anonim</span></label>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif

    {{-- Question Form Modal --}}
    @if($showQuestionForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showQuestionForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[480px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">Tambah Pertanyaan</h3></div>
                <form wire:submit="saveQuestion" class="p-6 flex flex-col gap-4">
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Tipe Pertanyaan</label><select wire:model="questionType" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="rating">⭐ Rating (1-5)</option><option value="text">✏️ Teks Bebas</option><option value="nps">📊 NPS (0-10)</option><option value="multiple_choice">📋 Pilihan Ganda</option></select></div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Pertanyaan <span class="text-danger">*</span></label><textarea wire:model="questionContent" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]"></textarea>@error('questionContent')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" wire:model="questionRequired" class="accent-brand w-4 h-4"><span class="text-sm text-slate-700 font-medium">Wajib dijawab</span></label>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showQuestionForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Simpan</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
