<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $examTitle ?? 'Tes Psikologi' }} — HR Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="m-0 bg-slate-50 min-h-screen font-[Inter,system-ui,sans-serif] antialiased">
    {{-- Minimal Top Bar --}}
    <header class="h-14 bg-white border-b border-slate-100 flex items-center justify-between px-6 sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand to-brand-dark flex items-center justify-center">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                    <path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96.44 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 1.98-3A2.5 2.5 0 0 1 9.5 2z"/>
                    <path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-1.98-3A2.5 2.5 0 0 0 14.5 2z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">HR Solutions — Tes Psikologi</span>
        </div>

        @hasSection('timer')
            <div id="exam-timer" class="flex items-center gap-2">
                @yield('timer')
            </div>
        @endif
    </header>

    {{-- Main Content --}}
    <main class="max-w-4xl mx-auto py-8 px-4">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
