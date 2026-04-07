<div>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <button wire:click="previousMonth" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h2 class="text-xl font-bold text-white">{{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}</h2>
            <button wire:click="nextMonth" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
        <select wire:model.live="departmentId" class="rounded-lg bg-white/5 border border-white/10 text-sm text-white px-3 py-2">
            <option value="">Semua Departemen</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </select>
    </div>

    <x-ui.card>
        <div class="grid grid-cols-7 gap-px bg-white/5 rounded-lg overflow-hidden">
            @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $day)
                <div class="text-center text-xs font-semibold text-slate-400 py-2 bg-slate-800/80">{{ $day }}</div>
            @endforeach

            @php
                $current = $startDate->copy()->startOfWeek();
                $endOfCalendar = $endDate->copy()->endOfWeek();
            @endphp

            @while($current->lte($endOfCalendar))
                @php $key = $current->toDateString(); @endphp
                <div class="min-h-[80px] p-1.5 {{ $current->month !== $month ? 'bg-slate-900/50' : 'bg-slate-800/50' }} {{ $current->isToday() ? 'ring-1 ring-cyan-500' : '' }}">
                    <span class="text-xs {{ $current->isToday() ? 'text-cyan-400 font-bold' : 'text-slate-500' }}">{{ $current->day }}</span>
                    @if(isset($calendarData[$key]))
                        @foreach(array_slice($calendarData[$key], 0, 3) as $leave)
                            <div class="mt-0.5 px-1.5 py-0.5 rounded text-[10px] truncate" style="background: {{ $leave['color'] }}22; color: {{ $leave['color'] }}">
                                {{ $leave['employee'] }}
                            </div>
                        @endforeach
                        @if(count($calendarData[$key]) > 3)
                            <span class="text-[10px] text-slate-500">+{{ count($calendarData[$key]) - 3 }} lagi</span>
                        @endif
                    @endif
                </div>
                @php $current->addDay(); @endphp
            @endwhile
        </div>
    </x-ui.card>
</div>
