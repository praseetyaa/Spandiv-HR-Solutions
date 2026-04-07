<div>
    <div class="mb-6 flex items-center gap-4">
        <h2 class="text-xl font-bold text-white">Struktur Organisasi</h2>
        <select wire:model.live="departmentId" class="rounded-lg bg-white/5 border border-white/10 text-sm text-white px-3 py-2 focus:ring-cyan-500">
            <option value="">Semua Departemen</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($tree as $node)
            <x-ui.card>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white">{{ $node['department']->name }}</h3>
                        <p class="text-xs text-slate-400">{{ $node['employees']->count() }} anggota</p>
                    </div>
                </div>

                @if($node['head'])
                    <div class="mb-3 p-3 rounded-lg bg-gradient-to-r from-amber-500/10 to-orange-500/10 border border-amber-500/20">
                        <p class="text-xs text-amber-400 font-semibold uppercase tracking-wider mb-1">Kepala</p>
                        <p class="text-sm text-white font-medium">{{ $node['head']->full_name }}</p>
                        <p class="text-xs text-slate-400">{{ $node['head']->position?->title }}</p>
                    </div>
                @endif

                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($node['employees'] as $emp)
                        @if($emp->id !== $node['head']?->id)
                            <div class="flex items-center gap-2 py-1.5">
                                <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold text-cyan-400">
                                    {{ strtoupper(substr($emp->first_name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm text-white/90 truncate">{{ $emp->full_name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ $emp->position?->title }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </x-ui.card>
        @endforeach
    </div>
</div>
