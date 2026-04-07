@extends('layouts.exam', ['examTitle' => $test->name])

@section('timer')
    <div x-data="examTimer({{ $test->duration_minutes * 60 }})" x-init="start()"
         class="flex items-center gap-2 px-3 py-1.5 rounded-lg"
         x-bind:class="remaining < 300 ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-600'">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
        <span class="text-sm font-semibold tabular-nums" x-text="display"></span>
    </div>
@endsection

@section('content')
    <div x-data="examApp()" x-init="initExam()">
        {{-- Candidate Header --}}
        <div class="bg-white rounded-xl border border-slate-100 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Peserta Tes</div>
                    <div class="text-lg font-bold text-slate-800 mt-1">{{ $candidate->name }}</div>
                    <div class="text-sm text-slate-400 mt-0.5">{{ $test->name }} — {{ $test->total_questions }} soal, {{ $test->duration_minutes }} menit</div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-slate-400">Progres</div>
                    <div class="text-lg font-bold text-brand" x-text="answeredCount + '/' + totalQuestions"></div>
                </div>
            </div>
            {{-- Progress Bar --}}
            <div class="w-full h-2 rounded-full bg-slate-100 mt-4 overflow-hidden">
                <div class="h-full rounded-full bg-brand transition-all duration-500"
                     x-bind:style="'width: ' + ((answeredCount / totalQuestions) * 100) + '%'"></div>
            </div>
        </div>

        {{-- Question Card --}}
        <div class="bg-white rounded-xl border border-slate-100 overflow-hidden mb-6">
            {{-- Question Header --}}
            <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-600">
                    Soal <span x-text="currentIndex + 1"></span> dari <span x-text="totalQuestions"></span>
                </span>
                <button @click="toggleFlag()" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all"
                        x-bind:class="questions[currentIndex]?.flagged ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-500 border border-slate-100'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" x2="4" y1="22" y2="15"/></svg>
                    <span x-text="questions[currentIndex]?.flagged ? 'Ditandai' : 'Tandai'"></span>
                </button>
            </div>

            {{-- Question Body --}}
            <div class="p-6 md:p-8">
                <div class="text-base text-slate-800 leading-relaxed mb-6" x-html="questions[currentIndex]?.content"></div>

                {{-- Multiple Choice Options --}}
                <template x-if="questions[currentIndex]?.type === 'multiple_choice' || questions[currentIndex]?.type === 'true_false'">
                    <div class="space-y-3">
                        <template x-for="(option, oi) in questions[currentIndex].options" :key="option.id">
                            <button
                                @click="selectOption(option.id)"
                                class="w-full text-left p-4 rounded-xl border-2 transition-all duration-200 flex items-center gap-3"
                                x-bind:class="questions[currentIndex].selectedOptionId === option.id
                                    ? 'border-brand bg-brand-50 shadow-sm'
                                    : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50'"
                            >
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shrink-0 transition-all"
                                     x-bind:class="questions[currentIndex].selectedOptionId === option.id
                                         ? 'bg-brand text-white'
                                         : 'bg-slate-100 text-slate-500'">
                                    <span x-text="String.fromCharCode(65 + oi)"></span>
                                </div>
                                <span class="text-sm" x-bind:class="questions[currentIndex].selectedOptionId === option.id ? 'text-brand-dark font-medium' : 'text-slate-600'" x-text="option.content"></span>
                            </button>
                        </template>
                    </div>
                </template>

                {{-- Essay --}}
                <template x-if="questions[currentIndex]?.type === 'essay'">
                    <textarea
                        x-model="questions[currentIndex].answerText"
                        @input.debounce.1000ms="saveAnswer()"
                        class="w-full px-4 py-3 rounded-xl border-2 border-slate-100 text-sm text-slate-700 placeholder-slate-300 focus:outline-none focus:border-brand transition-colors resize-y min-h-[120px]"
                        placeholder="Tulis jawaban Anda di sini..."
                    ></textarea>
                </template>

                {{-- Number Input --}}
                <template x-if="questions[currentIndex]?.type === 'number_input' || questions[currentIndex]?.type === 'number_series'">
                    <input
                        type="number"
                        x-model="questions[currentIndex].numberInput"
                        @input.debounce.500ms="saveAnswer()"
                        class="w-full px-4 py-3 rounded-xl border-2 border-slate-100 text-sm text-slate-700 placeholder-slate-300 focus:outline-none focus:border-brand transition-colors"
                        placeholder="Masukkan angka..."
                    >
                </template>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="flex items-center justify-between">
            <button
                @click="prev()"
                x-show="currentIndex > 0"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-all cursor-pointer"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                Sebelumnya
            </button>
            <div x-show="currentIndex === 0"></div>

            <div class="flex items-center gap-3">
                <template x-if="currentIndex < totalQuestions - 1">
                    <button
                        @click="next()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand text-white text-sm font-semibold hover:bg-brand-dark transition-all cursor-pointer border-none shadow-lg shadow-brand/20"
                    >
                        Selanjutnya
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </template>
                <template x-if="currentIndex === totalQuestions - 1">
                    <button
                        @click="confirmFinish()"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-500 transition-all cursor-pointer border-none shadow-lg shadow-emerald-600/20"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                        Selesai & Kirim
                    </button>
                </template>
            </div>
        </div>

        {{-- Question Navigator --}}
        <div class="bg-white rounded-xl border border-slate-100 p-6 mt-6">
            <div class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-3">Navigasi Soal</div>
            <div class="flex flex-wrap gap-2">
                <template x-for="(q, i) in questions" :key="i">
                    <button
                        @click="goTo(i)"
                        class="w-9 h-9 rounded-lg text-xs font-semibold transition-all cursor-pointer border"
                        x-bind:class="{
                            'bg-brand text-white border-brand': currentIndex === i,
                            'bg-brand-50 text-brand border-brand-100': currentIndex !== i && q.selectedOptionId,
                            'bg-amber-50 text-amber-600 border-amber-200': currentIndex !== i && q.flagged && !q.selectedOptionId,
                            'bg-slate-50 text-slate-400 border-slate-100 hover:bg-slate-100': currentIndex !== i && !q.selectedOptionId && !q.flagged,
                        }"
                        x-text="i + 1"
                    ></button>
                </template>
            </div>
            <div class="flex items-center gap-4 mt-3 text-xs text-slate-400">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-brand-50 border border-brand-100"></span> Terjawab</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-amber-50 border border-amber-200"></span> Ditandai</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-slate-50 border border-slate-100"></span> Belum</span>
            </div>
        </div>

        {{-- Tab Switch Warning Modal --}}
        <div x-show="showTabWarning" x-transition.opacity class="fixed inset-0 bg-black/50 flex items-center justify-center z-[100]" style="display: none;">
            <div class="bg-white rounded-2xl p-6 max-w-sm mx-4 shadow-2xl text-center">
                <div class="w-14 h-14 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-4">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Peringatan!</h3>
                <p class="text-sm text-slate-500 mb-4">Anda meninggalkan halaman tes. Pelanggaran ke-<span x-text="tabSwitchCount"></span> dari <span x-text="tabSwitchMax"></span> yang diperbolehkan.</p>
                <button @click="showTabWarning = false" class="px-5 py-2.5 rounded-xl bg-brand text-white text-sm font-semibold border-none cursor-pointer hover:bg-brand-dark transition-all">Kembali ke Tes</button>
            </div>
        </div>

        {{-- Confirm Finish Modal --}}
        <div x-show="showFinishModal" x-transition.opacity class="fixed inset-0 bg-black/50 flex items-center justify-center z-[100]" style="display: none;">
            <div class="bg-white rounded-2xl p-6 max-w-sm mx-4 shadow-2xl text-center">
                <h3 class="text-lg font-bold text-slate-800 mb-2">Kirim Jawaban?</h3>
                <p class="text-sm text-slate-500 mb-1">Terjawab: <span class="font-bold text-brand" x-text="answeredCount"></span> / <span x-text="totalQuestions"></span></p>
                <p class="text-sm text-slate-500 mb-4" x-show="unansweredCount > 0">
                    <span class="text-amber-500 font-semibold" x-text="unansweredCount"></span> soal belum dijawab.
                </p>
                <div class="flex items-center gap-3 justify-center">
                    <button @click="showFinishModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-sm font-medium border-none cursor-pointer hover:bg-slate-200 transition-all">Kembali</button>
                    <button @click="finishExam()" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold border-none cursor-pointer hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-600/20">Ya, Kirim</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function examTimer(totalSeconds) {
        return {
            remaining: totalSeconds,
            display: '',
            interval: null,
            start() {
                this.tick();
                this.interval = setInterval(() => this.tick(), 1000);
            },
            tick() {
                if (this.remaining <= 0) {
                    clearInterval(this.interval);
                    this.display = '00:00';
                    // Auto-submit on timeout
                    document.querySelector('[x-data*="examApp"]')?.__x?.$data?.finishExam?.();
                    return;
                }
                this.remaining--;
                const m = Math.floor(this.remaining / 60);
                const s = this.remaining % 60;
                this.display = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            }
        };
    }

    function examApp() {
        return {
            sessionId: {{ $session->id }},
            currentIndex: 0,
            totalQuestions: 0,
            questions: [],
            showTabWarning: false,
            showFinishModal: false,
            tabSwitchCount: {{ $session->tab_switch_count ?? 0 }},
            tabSwitchMax: {{ config('hr.psych_test.tab_switch_threshold', 3) }},

            get answeredCount() {
                return this.questions.filter(q => q.selectedOptionId || q.answerText || q.numberInput).length;
            },
            get unansweredCount() {
                return this.totalQuestions - this.answeredCount;
            },

            initExam() {
                const raw = @json($test->sections->flatMap(fn($s) => $s->questions->map(fn($q) => [
                    'id' => $q->id,
                    'type' => $q->type,
                    'content' => $q->content,
                    'options' => $q->options->map(fn($o) => ['id' => $o->id, 'content' => $o->content]),
                ]))->values());

                this.questions = raw.map(q => ({
                    ...q,
                    selectedOptionId: null,
                    answerText: '',
                    numberInput: null,
                    flagged: false,
                }));
                this.totalQuestions = this.questions.length;

                // Tab switch detection
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) this.reportTabSwitch();
                });
            },

            selectOption(optionId) {
                this.questions[this.currentIndex].selectedOptionId = optionId;
                this.saveAnswer();
            },
            toggleFlag() {
                this.questions[this.currentIndex].flagged = !this.questions[this.currentIndex].flagged;
            },
            prev() { if (this.currentIndex > 0) this.currentIndex--; },
            next() { if (this.currentIndex < this.totalQuestions - 1) this.currentIndex++; },
            goTo(i) { this.currentIndex = i; },
            confirmFinish() { this.showFinishModal = true; },

            async saveAnswer() {
                const q = this.questions[this.currentIndex];
                await fetch('{{ route("test.answer") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId,
                        question_id: q.id,
                        selected_option_id: q.selectedOptionId,
                        answer_text: q.answerText || null,
                        number_input: q.numberInput,
                        time_spent_sec: 0,
                    }),
                });
            },

            async reportTabSwitch() {
                const res = await fetch(`/test/${this.sessionId}/tab-switch`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                });
                const data = await res.json();
                this.tabSwitchCount = data.count || this.tabSwitchCount + 1;
                if (data.status === 'disqualified') {
                    window.location.href = '/test/completed?status=disqualified';
                } else {
                    this.showTabWarning = true;
                }
            },

            async finishExam() {
                this.showFinishModal = false;
                await fetch(`/test/${this.sessionId}/finish`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                });
                window.location.href = `/test/${this.sessionId}/finish`;
            },
        };
    }
</script>
@endpush
