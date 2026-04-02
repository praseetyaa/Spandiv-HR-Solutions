<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">🏆 Wall of Recognition</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Berikan apresiasi dan pengakuan kepada rekan kerja</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-amber-500 to-orange-500 text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Beri Rekognisi
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-5 border border-amber-100">
            <div class="flex items-center gap-3">
                <div class="text-3xl">🏅</div>
                <div>
                    <div class="text-2xl font-bold text-amber-700">{{ $this->totalRecognitions }}</div>
                    <div class="text-xs text-amber-500">Total Rekognisi</div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-5 border border-purple-100">
            <div class="flex items-center gap-3">
                <div class="text-3xl">⭐</div>
                <div>
                    <div class="text-2xl font-bold text-purple-700">{{ number_format($this->totalPoints) }}</div>
                    <div class="text-xs text-purple-500">Total Poin</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Badge filter --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative max-w-[280px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="form-input w-full py-2 pl-9 pr-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
        </div>
        <select wire:model.live="badgeFilter" class="form-input py-2 px-3 border border-slate-200 rounded-lg text-sm outline-none bg-white">
            <option value="">Semua Badge</option>
            <option value="teamwork">🤝 Teamwork</option>
            <option value="innovation">💡 Inovasi</option>
            <option value="leadership">👑 Kepemimpinan</option>
            <option value="dedication">💪 Dedikasi</option>
            <option value="customer_focus">🎯 Customer Focus</option>
            <option value="above_beyond">🚀 Above & Beyond</option>
        </select>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recognition Feed --}}
        <div class="lg:col-span-2">
            <div class="space-y-4">
                @forelse($this->recognitions as $rec)
                    <x-ui.card>
                        <div class="flex items-start gap-4">
                            <div class="text-3xl shrink-0">{{ $rec->badge_emoji }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">{{ $rec->badge_label }}</span>
                                    <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">+{{ $rec->points }} pts</span>
                                </div>
                                <p class="text-sm text-slate-700 mb-2 m-0">{{ $rec->message }}</p>
                                <div class="flex items-center gap-3 text-xs text-slate-400">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center text-[9px] font-bold text-brand-700">{{ $rec->giver->initials }}</div>
                                        <span>{{ $rec->giver->full_name }}</span>
                                    </div>
                                    <span>→</span>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-full bg-amber-100 flex items-center justify-center text-[9px] font-bold text-amber-700">{{ $rec->receiver->initials }}</div>
                                        <span class="font-medium text-slate-600">{{ $rec->receiver->full_name }}</span>
                                    </div>
                                    <span class="ml-auto">{{ $rec->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </x-ui.card>
                @empty
                    <div class="text-center py-12 text-slate-400">
                        <div class="text-4xl mb-2">🌟</div>
                        <div class="font-medium text-slate-600 mb-1">Belum Ada Rekognisi</div>
                        <div class="text-sm">Jadilah yang pertama mengapresiasi rekan kerja!</div>
                        <button wire:click="openForm" class="mt-3 px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-lg border-none cursor-pointer hover:bg-amber-600 transition-colors">Beri Rekognisi</button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Leaderboard --}}
        <div>
            <x-ui.card>
                <div class="p-1">
                    <h3 class="m-0 text-[15px] font-bold text-slate-900 mb-4">🏆 Leaderboard</h3>
                    <div class="space-y-3">
                        @forelse($this->leaderboard as $index => $emp)
                            <div class="flex items-center gap-3">
                                <div class="w-6 text-center">
                                    @if($index === 0)<span class="text-lg">🥇</span>
                                    @elseif($index === 1)<span class="text-lg">🥈</span>
                                    @elseif($index === 2)<span class="text-lg">🥉</span>
                                    @else<span class="text-xs font-bold text-slate-400">#{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-700">{{ $emp->initials }}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-slate-900 truncate">{{ $emp->full_name }}</div>
                                    <div class="text-xs text-slate-400">{{ $emp->total_recognitions }} rekognisi</div>
                                </div>
                                <div class="text-sm font-bold text-amber-600">{{ $emp->total_points }} pts</div>
                            </div>
                        @empty
                            <div class="text-sm text-slate-400 text-center py-4">Belum ada data.</div>
                        @endforelse
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- Form Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6" wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-w-[520px]">
                <div class="px-6 py-5 border-b border-slate-100"><h3 class="m-0 text-[17px] font-semibold text-slate-900">🌟 Beri Rekognisi</h3></div>
                <form wire:submit="save" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Dari <span class="text-danger">*</span></label><select wire:model="giverId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach</select>@error('giverId')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                        <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Untuk <span class="text-danger">*</span></label><select wire:model="receiverId" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none bg-white"><option value="">Pilih...</option>@foreach($this->employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach</select>@error('receiverId')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Badge</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['teamwork' => '🤝 Teamwork', 'innovation' => '💡 Inovasi', 'leadership' => '👑 Leader', 'dedication' => '💪 Dedikasi', 'customer_focus' => '🎯 Customer', 'above_beyond' => '🚀 Beyond'] as $value => $label)
                                <button type="button" wire:click="$set('badgeType', '{{ $value }}')" class="p-2 rounded-xl border text-xs font-medium cursor-pointer transition-all {{ $badgeType === $value ? 'bg-amber-50 border-amber-300 text-amber-700' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div><label class="block mb-1.5 text-[13px] font-semibold text-slate-700">Pesan <span class="text-danger">*</span></label><textarea wire:model="message" rows="3" class="form-input w-full py-2.5 px-3.5 border border-slate-200 rounded-[10px] text-sm outline-none resize-y font-[Inter,sans-serif]" placeholder="Ceritakan kenapa mereka pantas mendapat apresiasi..."></textarea>@error('message')<p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>@enderror</div>
                    <div class="flex justify-end gap-2.5 pt-2"><button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-[10px] border border-slate-200 bg-white text-slate-700 text-sm font-semibold cursor-pointer hover:bg-slate-50">Batal</button><button type="submit" class="px-5 py-2.5 rounded-[10px] border-none bg-gradient-to-br from-amber-500 to-orange-500 text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">Kirim 🎉</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
