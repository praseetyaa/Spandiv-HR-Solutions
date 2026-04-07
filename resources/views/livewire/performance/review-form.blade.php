<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">Form Penilaian Kinerja</h2>
        <div class="flex gap-2">
            <button wire:click="saveDraft" class="px-4 py-2 rounded-lg bg-white/10 text-white text-sm hover:bg-white/20 transition">💾 Simpan Draft</button>
            <button wire:click="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-500 to-green-600 text-white text-sm font-medium hover:shadow-lg transition">✅ Submit</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Employee Info & Config --}}
        <div class="space-y-4">
            <x-ui.card>
                <h3 class="text-sm font-semibold text-slate-400 uppercase mb-3">Informasi</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-slate-500">Siklus Review</label>
                        <select wire:model="cycleId" class="w-full mt-1 rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2">
                            <option value="">Pilih Siklus</option>
                            @foreach($cycles as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if($employee)
                    <div class="mt-4 p-3 rounded-lg bg-white/5">
                        <p class="text-white font-medium">{{ $employee->full_name }}</p>
                        <p class="text-xs text-slate-400">{{ $employee->position?->title }} • {{ $employee->department?->name }}</p>
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-semibold text-slate-400 uppercase mb-3">Skor Keseluruhan</h3>
                <div class="text-center">
                    <div class="text-5xl font-black {{ $overallScore >= 80 ? 'text-emerald-400' : ($overallScore >= 60 ? 'text-amber-400' : 'text-rose-400') }}">
                        {{ number_format($overallScore, 1) }}
                    </div>
                    <p class="text-xs text-slate-400 mt-1">dari 5.0</p>
                </div>
            </x-ui.card>
        </div>

        {{-- Right: Scoring Grid --}}
        <div class="lg:col-span-2 space-y-4">
            <x-ui.card>
                <h3 class="text-sm font-semibold text-slate-400 uppercase mb-4">Penilaian per Kriteria</h3>
                <div class="space-y-4">
                    @foreach($criteriaList as $key => $label)
                        <div class="flex items-center gap-4">
                            <span class="w-40 text-sm text-white">{{ $label }}</span>
                            <div class="flex-1 flex items-center gap-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <button wire:click="$set('scores.{{ $key }}', {{ $i }})"
                                            class="w-10 h-10 rounded-lg text-sm font-bold transition
                                                {{ ($scores[$key] ?? 0) >= $i ? 'bg-gradient-to-br from-cyan-500 to-blue-600 text-white shadow-lg shadow-cyan-500/20' : 'bg-white/5 text-slate-500 hover:bg-white/10' }}">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                            <span class="w-8 text-center text-sm font-bold {{ ($scores[$key] ?? 0) >= 4 ? 'text-emerald-400' : (($scores[$key] ?? 0) >= 3 ? 'text-amber-400' : 'text-slate-500') }}">
                                {{ $scores[$key] ?? '-' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-semibold text-slate-400 uppercase mb-3">Komentar</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-slate-500">Kekuatan</label>
                        <textarea wire:model="strengths" rows="2" class="w-full mt-1 rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2"></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Area Pengembangan</label>
                        <textarea wire:model="improvements" rows="2" class="w-full mt-1 rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2"></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500">Komentar Umum</label>
                        <textarea wire:model="overallComment" rows="2" class="w-full mt-1 rounded-lg bg-white/5 border border-white/10 text-white text-sm px-3 py-2"></textarea>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
