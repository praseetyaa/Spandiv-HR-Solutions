<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — HR Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .login-bg {
            background: linear-gradient(135deg, #0C172E 0%, #1E3F75 40%, #2B5BA8 70%, #6792D6 100%);
        }

        .login-bg::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(ellipse at 30% 50%, rgba(43, 91, 168, 0.15) 0%, transparent 60%),
                        radial-gradient(ellipse at 70% 20%, rgba(103, 146, 214, 0.1) 0%, transparent 50%);
            animation: bgPulse 15s ease-in-out infinite;
        }

        @keyframes bgPulse {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(2%, -1%) scale(1.02); }
            66% { transform: translate(-1%, 2%) scale(0.98); }
        }

        .float-shape { animation-fill-mode: both; }
        .float-shape:nth-child(1) { width: 300px; height: 300px; top: 10%; left: -5%; animation: floatA 20s ease-in-out infinite; }
        .float-shape:nth-child(2) { width: 200px; height: 200px; top: 60%; right: -3%; animation: floatB 25s ease-in-out infinite; }
        .float-shape:nth-child(3) { width: 150px; height: 150px; bottom: 10%; left: 30%; animation: floatA 18s ease-in-out infinite reverse; }

        @keyframes floatA {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -20px); }
        }
        @keyframes floatB {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-20px, 30px); }
        }

        /* Glass card */
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        /* Input fields */
        .login-input {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
        }
        .login-input::placeholder { color: rgba(255, 255, 255, 0.4); }
        .login-input:focus {
            border-color: rgba(103, 146, 214, 0.6);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 4px rgba(43, 91, 168, 0.2);
        }

        .input-group:focus-within .input-icon { color: rgba(103, 146, 214, 0.8); }

        /* Button animation */
        .btn-login::before {
            content: '';
            position: absolute; top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s;
        }
        .btn-login:hover::before { left: 100%; }
        .btn-login:active { transform: translateY(0); }

        /* Error state */
        .login-input.error {
            border-color: rgba(220, 38, 38, 0.6);
            background: rgba(220, 38, 38, 0.08);
        }

        /* Grid lines decoration */
        .grid-lines {
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }
    </style>
</head>
<body class="login-bg flex items-center justify-center min-h-screen relative overflow-hidden">
    <!-- Floating shapes -->
    <div class="float-shape absolute rounded-full opacity-5 bg-white"></div>
    <div class="float-shape absolute rounded-full opacity-5 bg-white"></div>
    <div class="float-shape absolute rounded-full opacity-5 bg-white"></div>

    <!-- Grid overlay -->
    <div class="grid-lines absolute top-0 left-0 w-full h-full pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-[440px] px-6">
        <!-- Logo + Title -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-brand to-brand-400 rounded-2xl mb-5 shadow-[0_8px_32px_rgba(43,91,168,0.3)]">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <h1 class="text-white text-[28px] font-bold m-0 mb-2">HR Solutions</h1>
            <p class="text-white/50 text-[15px] m-0">Masuk ke akun Anda</p>
        </div>

        <!-- Login Card -->
        <div class="glass-card rounded-3xl p-10">
            @if(session('success'))
                <div class="bg-green-600/15 border border-green-600/30 rounded-xl px-4 py-3 text-green-300 text-sm mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-600/15 border border-red-600/30 rounded-xl px-4 py-3 text-red-300 text-sm mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" id="loginForm">
                @csrf

                <!-- Email -->
                <div class="mb-5">
                    <label class="block text-white/60 text-[13px] font-medium mb-2 tracking-wide uppercase" for="email">Email</label>
                    <div class="input-group relative">
                        <div class="input-icon absolute left-4 top-1/2 -translate-y-1/2 text-white/35 pointer-events-none transition-colors duration-300">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2"/>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="login-input w-full py-3.5 pl-12 pr-4 rounded-xl text-white text-[15px] font-[Inter,sans-serif] outline-none box-border @error('email') error @enderror"
                            placeholder="nama@perusahaan.com"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
                        <p class="text-red-300 text-[13px] mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label class="block text-white/60 text-[13px] font-medium mb-2 tracking-wide uppercase" for="password">Password</label>
                    <div class="input-group relative">
                        <div class="input-icon absolute left-4 top-1/2 -translate-y-1/2 text-white/35 pointer-events-none transition-colors duration-300">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="login-input w-full py-3.5 pl-12 pr-4 rounded-xl text-white text-[15px] font-[Inter,sans-serif] outline-none box-border @error('password') error @enderror"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    @error('password')
                        <p class="text-red-300 text-[13px] mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-8">
                    <label class="flex items-center gap-2 cursor-pointer text-white/60 text-sm">
                        <input type="checkbox" name="remember" class="accent-brand w-4 h-4 cursor-pointer" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login w-full py-3.5 px-6 bg-gradient-to-br from-brand to-brand-400 border-none rounded-xl text-white text-base font-semibold font-[Inter,sans-serif] cursor-pointer transition-all duration-300 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-[0_8px_32px_rgba(43,91,168,0.4)]" id="btnLogin">
                    Masuk
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-white/30 text-[13px] mt-8">
            &copy; {{ date('Y') }} Spandiv HR Solutions. All rights reserved.
        </p>
    </div>
</body>
</html>
