<div class="relative" x-data="{ open: false }" @click.away="open = false">
    {{-- Bell Button --}}
    <button @click="open = !open" class="relative p-2 rounded-lg transition-all duration-200 hover:bg-white/10">
        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-red-500 rounded-full animate-pulse">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         class="absolute right-0 mt-2 w-80 rounded-xl border border-white/10 bg-slate-800/95 backdrop-blur-xl shadow-2xl z-50 overflow-hidden">

        <div class="flex items-center justify-between px-4 py-3 border-b border-white/10">
            <h3 class="text-sm font-semibold text-white">Notifikasi</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllRead" class="text-xs text-cyan-400 hover:text-cyan-300 transition">Tandai semua dibaca</button>
            @endif
        </div>

        <div class="max-h-72 overflow-y-auto divide-y divide-white/5">
            @forelse($notifications as $notif)
                <div wire:click="markAsRead({{ $notif->id }})"
                     class="px-4 py-3 cursor-pointer transition hover:bg-white/5 {{ !$notif->read_at ? 'bg-cyan-500/5 border-l-2 border-cyan-400' : '' }}">
                    <p class="text-sm text-white/90 font-medium truncate">{{ $notif->title }}</p>
                    <p class="text-xs text-slate-400 mt-0.5 line-clamp-2">{{ $notif->body }}</p>
                    <p class="text-[10px] text-slate-500 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm text-slate-500">Belum ada notifikasi</div>
            @endforelse
        </div>
    </div>
</div>
