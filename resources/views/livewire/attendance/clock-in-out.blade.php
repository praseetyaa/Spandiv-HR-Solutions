<div>
    <x-ui.card>
        <div class="text-center">
            <h3 class="text-lg font-bold text-white mb-1">Absensi Hari Ini</h3>
            <p class="text-xs text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</p>

            <div class="mt-6">
                @if($todayStatus === 'not_started')
                    <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center cursor-pointer transition-transform hover:scale-105 shadow-lg shadow-emerald-500/30"
                         wire:click="clockIn" wire:loading.class="animate-pulse">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <p class="mt-3 text-sm text-emerald-400 font-medium">Tap untuk Clock In</p>

                @elseif($todayStatus === 'clocked_in')
                    <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center cursor-pointer transition-transform hover:scale-105 shadow-lg shadow-rose-500/30"
                         wire:click="clockOut" wire:loading.class="animate-pulse">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <p class="mt-3 text-sm text-rose-400 font-medium">Tap untuk Clock Out</p>
                    <p class="text-xs text-slate-400 mt-1">Clock In: {{ $clockInTime }}</p>

                @else
                    <div class="w-24 h-24 mx-auto rounded-full bg-slate-700 flex items-center justify-center">
                        <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="mt-3 text-sm text-emerald-400 font-medium">Selesai untuk hari ini</p>
                    <div class="flex justify-center gap-4 mt-2 text-xs text-slate-400">
                        <span>In: {{ $clockInTime }}</span>
                        <span>Out: {{ $clockOutTime }}</span>
                    </div>
                @endif
            </div>

            @error('clock')
                <p class="mt-3 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </x-ui.card>
</div>
