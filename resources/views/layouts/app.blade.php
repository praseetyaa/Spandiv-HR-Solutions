<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Dashboard' }} — HR Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body x-data class="m-0 bg-[#F3F4F8] min-h-screen">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Mobile overlay --}}
        <div
            class="sidebar-overlay fixed inset-0 bg-black/50 z-40 hidden"
            x-bind:class="$store.sidebar.open ? 'is-open' : ''"
            x-on:click="$store.sidebar.toggle()"
        ></div>

        {{-- Main Content --}}
        <div
            class="hr-main flex-1 min-w-0"
            x-bind:style="$store.sidebar.open ? 'margin-left: 260px' : 'margin-left: 72px'"
        >
            {{-- Navbar --}}
            @include('components.navbar')

            {{-- Page Content --}}
            <main class="p-6 md:px-8">
                {{-- Breadcrumb --}}
                @hasSection('breadcrumb')
                    <div class="mb-2">
                        @yield('breadcrumb')
                    </div>
                @endif

                {{-- Flash Messages --}}
                @if(session('success'))
                    <x-ui.alert type="success" :message="session('success')" />
                @endif
                @if(session('error'))
                    <x-ui.alert type="error" :message="session('error')" />
                @endif
                @if(session('warning'))
                    <x-ui.alert type="warning" :message="session('warning')" />
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
