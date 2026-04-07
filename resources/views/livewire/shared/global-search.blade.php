<div class="relative" x-data @click.away="$wire.clear()">
    <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input wire:model.live.debounce.300ms="query" type="text"
               placeholder="Cari karyawan, departemen, lowongan..."
               class="w-64 pl-10 pr-4 py-2 text-sm rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 transition-all">
    </div>

    @if($showResults)
        <div class="absolute top-full left-0 mt-2 w-80 rounded-xl bg-slate-800/95 backdrop-blur-xl border border-white/10 shadow-2xl z-50 overflow-hidden">
            @foreach($results as $result)
                <a href="{{ $result['url'] }}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition border-b border-white/5 last:border-0">
                    <span class="shrink-0 px-2 py-0.5 text-[10px] font-semibold rounded-full
                        {{ $result['type'] === 'Karyawan' ? 'bg-cyan-500/20 text-cyan-400' : '' }}
                        {{ $result['type'] === 'Departemen' ? 'bg-purple-500/20 text-purple-400' : '' }}
                        {{ $result['type'] === 'Lowongan' ? 'bg-emerald-500/20 text-emerald-400' : '' }}">
                        {{ $result['type'] }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm text-white truncate">{{ $result['label'] }}</p>
                        @if($result['sub'])
                            <p class="text-xs text-slate-500">{{ $result['sub'] }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
