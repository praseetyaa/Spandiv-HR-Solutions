<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Platform Admin' }} — HR Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body x-data class="m-0 bg-[#0F1117] min-h-screen text-white">
    <div class="flex min-h-screen">
        {{-- Platform Sidebar --}}
        <aside
            class="platform-sidebar fixed top-0 left-0 bottom-0 z-50 flex flex-col overflow-hidden w-[260px]"
            x-bind:class="$store.sidebar.open ? 'w-[260px]' : 'w-[72px]'"
        >
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-white/[0.06] min-h-[72px] shrink-0">
                <div class="shrink-0 w-9 h-9 rounded-[10px] flex items-center justify-center bg-gradient-to-br from-violet-500 to-indigo-600 shadow-lg shadow-violet-500/30">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <div x-show="$store.sidebar.open" x-transition.opacity.duration.200ms class="overflow-hidden whitespace-nowrap">
                    <div class="text-white font-bold text-[15px] leading-tight">HR Solutions</div>
                    <div class="text-violet-400/60 text-[11px]">Platform Admin</div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-2 scrollbar-thin">
                @php
                    $platformMenu = [
                        ['label' => 'Dashboard', 'route' => 'platform.dashboard', 'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>'],
                        ['label' => 'Kelola Tenant', 'route' => 'platform.tenants.index', 'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
                        ['label' => 'Paket / Plan', 'route' => 'platform.plans.index', 'icon' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>'],
                        ['label' => 'Subscription', 'route' => 'platform.subscriptions.index', 'icon' => '<rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/><path d="M16 14h2"/>'],
                    ];
                @endphp

                @foreach ($platformMenu as $item)
                    @php $isActive = request()->routeIs($item['route']); @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] mb-0.5 no-underline text-sm font-normal transition-all duration-200 whitespace-nowrap overflow-hidden
                            {{ $isActive
                                ? 'bg-violet-500/20 text-violet-300 font-semibold'
                                : 'text-white/50 hover:bg-white/5 hover:text-white/80' }}"
                    >
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                {!! $item['icon'] !!}
                            </svg>
                        </div>
                        <span x-show="$store.sidebar.open" x-transition.opacity.duration.200ms>{{ $item['label'] }}</span>
                    </a>
                @endforeach

                <div class="h-px bg-white/[0.06] mx-3 my-4"></div>

                {{-- Back to impersonation or other links --}}
                @if(session('impersonating_from'))
                    <form method="POST" action="{{ route('platform.impersonate.stop') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-[10px] mb-0.5 text-orange-400/80 hover:bg-orange-500/10 text-sm transition-all duration-200 border-none bg-transparent cursor-pointer font-[inherit]">
                            <div class="shrink-0 w-5 h-5 flex items-center justify-center">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                                </svg>
                            </div>
                            <span x-show="$store.sidebar.open">Stop Impersonasi</span>
                        </button>
                    </form>
                @endif
            </nav>

            {{-- User Info --}}
            <div class="px-4 py-3 border-t border-white/[0.06] shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-xs font-bold text-white shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div x-show="$store.sidebar.open" class="min-w-0">
                        <div class="text-sm font-medium text-white/90 truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div class="text-xs text-white/40 truncate">Super Admin</div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div
            class="flex-1 min-w-0"
            x-bind:style="$store.sidebar.open ? 'margin-left: 260px' : 'margin-left: 72px'"
            style="transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
        >
            {{-- Top Bar --}}
            <header class="h-16 bg-[#161822] border-b border-white/[0.06] flex items-center justify-between px-6 sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <button
                        x-on:click="$store.sidebar.toggle()"
                        class="p-2 rounded-lg hover:bg-white/5 text-white/50 border-none bg-transparent cursor-pointer"
                    >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-white/90">{{ $pageTitle ?? 'Platform Admin' }}</h1>
                </div>

                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-violet-500/20 text-violet-300 border border-violet-500/30">
                        PLATFORM
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 rounded-lg hover:bg-white/5 text-white/50 border-none bg-transparent cursor-pointer transition-colors">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="p-6 md:px-8">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                         class="mb-4 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm rounded-xl flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="text-emerald-400 hover:text-emerald-300 border-none bg-transparent cursor-pointer">✕</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 px-4 py-3 bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-xl">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
