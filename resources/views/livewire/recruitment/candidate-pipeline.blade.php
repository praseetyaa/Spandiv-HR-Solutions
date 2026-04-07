<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">Pipeline Kandidat</h2>
        <select wire:model.live="jobId" class="rounded-lg bg-white/5 border border-white/10 text-sm text-white px-3 py-2">
            <option value="">Semua Lowongan</option>
            @foreach($jobs as $job)
                <option value="{{ $job->id }}">{{ $job->title }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex gap-4 overflow-x-auto pb-4">
        @foreach($pipeline as $stage => $candidates)
            <div class="min-w-[260px] flex-shrink-0">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider">
                        {{ str_replace('_', ' ', $stage) }}
                    </h3>
                    <span class="text-xs bg-white/10 text-slate-300 px-2 py-0.5 rounded-full">{{ $candidates->count() }}</span>
                </div>
                <div class="space-y-2">
                    @forelse($candidates as $candidate)
                        <div class="p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition cursor-pointer group">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-xs font-bold text-white">
                                    {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm text-white font-medium truncate">{{ $candidate->name }}</p>
                                    <p class="text-xs text-slate-400 truncate">{{ $candidate->jobPosting?->title }}</p>
                                </div>
                            </div>
                            <div class="mt-2 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                @foreach(['screening','interview','assessment','offering','hired'] as $s)
                                    @if($s !== $stage)
                                        <button wire:click="moveCandidate({{ $candidate->id }}, '{{ $s }}')"
                                                class="text-[10px] px-1.5 py-0.5 rounded bg-white/10 text-slate-300 hover:bg-cyan-500/20 hover:text-cyan-400 transition">
                                            → {{ ucfirst($s) }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-500 bg-white/5 rounded-xl border border-dashed border-white/10">
                            Belum ada kandidat
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
