<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Langganan Berakhir — HR Solutions</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-[Inter,sans-serif] bg-[#F3F4F8] flex items-center justify-center min-h-screen p-6">
    <div class="text-center max-w-[480px]">
        <div class="w-20 h-20 rounded-3xl bg-amber-100 mx-auto mb-6 flex items-center justify-center">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <h1 class="text-2xl text-slate-900 mb-3">Langganan Anda Telah Berakhir</h1>
        <p class="text-[15px] text-slate-500 leading-relaxed mb-8">Mohon maaf, akses Anda telah dibatasi karena langganan telah kedaluwarsa. Silakan hubungi administrator untuk memperpanjang langganan Anda.</p>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="btn-lift inline-flex items-center gap-2 px-7 py-3 text-[15px] font-semibold bg-gradient-to-br from-brand to-[#3468B8] text-white border-none rounded-xl cursor-pointer no-underline transition-all duration-200">Kembali ke Login</button>
        </form>
    </div>
</body>
</html>
