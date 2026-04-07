<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 m-0">API Management</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">Kelola akses API untuk integrasi website eksternal</p>
        </div>
    </div>

    {{-- Success message --}}
    @if(session('api_success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 font-medium flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('api_success') }}
        </div>
    @endif

    @if(session('api_warning'))
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700 font-medium flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            {{ session('api_warning') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main API Token Card --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Token Status --}}
            <x-ui.card>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 {{ $hasToken ? 'bg-emerald-100' : 'bg-slate-100' }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="{{ $hasToken ? '#059669' : '#94A3B8' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-lg font-bold text-slate-900 m-0">API Token</h3>
                            @if($hasToken)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-500">
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                    Belum Dibuat
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 m-0">
                            @if($hasToken)
                                Token digunakan untuk mengautentikasi request API dari website eksternal Anda.
                            @else
                                Buat API token untuk mengaktifkan integrasi dengan website eksternal.
                            @endif
                        </p>
                    </div>
                </div>

                @if($hasToken)
                    {{-- Masked token display --}}
                    <div class="mt-5 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Token Hash (masked)</div>
                                <code class="text-sm text-slate-700 font-mono tracking-wide">{{ $maskedToken }}</code>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="confirmRegenerate" class="px-4 py-2 rounded-lg border border-amber-200 bg-amber-50 text-amber-700 text-xs font-semibold cursor-pointer transition-all duration-200 hover:bg-amber-100">
                                    🔄 Regenerate
                                </button>
                                <button wire:click="revokeToken" wire:confirm="Apakah Anda yakin ingin mencabut API token? Semua integrasi akan berhenti berfungsi." class="px-4 py-2 rounded-lg border border-red-200 bg-red-50 text-red-600 text-xs font-semibold cursor-pointer transition-all duration-200 hover:bg-red-100">
                                    🗑️ Revoke
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Generate button --}}
                    <div class="mt-5">
                        <button wire:click="generateToken" class="px-6 py-3 rounded-xl border-none bg-gradient-to-br from-brand to-[#3468B8] text-white text-sm font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200">
                            🔑 Generate API Token
                        </button>
                    </div>
                @endif
            </x-ui.card>

            {{-- Regenerate Confirmation --}}
            @if($showRegenerateConfirm)
                <div class="p-5 bg-amber-50 border-2 border-amber-300 rounded-2xl">
                    <div class="flex items-start gap-3">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" class="shrink-0 mt-0.5">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-bold text-amber-800 m-0 mb-1">Konfirmasi Regenerasi Token</h4>
                            <p class="text-sm text-amber-700 m-0 mb-4">Token lama akan <strong>dihapus permanen</strong> dan tidak bisa dikembalikan. Semua integrasi yang menggunakan token lama akan berhenti berfungsi hingga Anda mengganti dengan token baru.</p>
                            <div class="flex items-center gap-3">
                                <button wire:click="regenerateToken" class="px-5 py-2.5 rounded-lg border-none bg-amber-600 text-white text-sm font-semibold cursor-pointer hover:bg-amber-700 transition-all duration-200">
                                    Ya, Regenerate Token
                                </button>
                                <button wire:click="cancelRegenerate" class="px-5 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-medium cursor-pointer hover:bg-slate-50 transition-all duration-200">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Newly generated token (shown once) --}}
            @if($newPlaintextToken)
                <div class="p-5 bg-emerald-50 border-2 border-emerald-300 rounded-2xl" x-data="{ copied: false }">
                    <div class="flex items-start gap-3 mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" class="shrink-0 mt-0.5">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <div>
                            <h4 class="font-bold text-emerald-800 m-0 mb-1">Token Berhasil Dibuat!</h4>
                            <p class="text-sm text-emerald-700 m-0">Salin token di bawah — <strong>token hanya ditampilkan satu kali ini saja</strong>.</p>
                        </div>
                    </div>

                    <div class="p-4 bg-white border border-emerald-200 rounded-xl flex items-center gap-3">
                        <code class="flex-1 text-sm text-slate-800 font-mono break-all select-all" id="api-token-value">{{ $newPlaintextToken }}</code>
                        <button
                            x-on:click="navigator.clipboard.writeText('{{ $newPlaintextToken }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="shrink-0 px-4 py-2.5 rounded-lg border-none text-sm font-semibold cursor-pointer transition-all duration-200"
                            x-bind:class="copied ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-white hover:bg-slate-700'"
                        >
                            <span x-show="!copied">📋 Salin</span>
                            <span x-show="copied">✅ Disalin!</span>
                        </button>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-xs text-emerald-600 m-0">⚠️ Setelah menutup pesan ini, token tidak bisa dilihat lagi.</p>
                        <button wire:click="dismissToken" class="text-xs text-emerald-700 font-semibold cursor-pointer bg-transparent border-none hover:text-emerald-900">
                            Tutup ✕
                        </button>
                    </div>
                </div>
            @endif

            {{-- API Documentation --}}
            <x-ui.card title="Dokumentasi API">
                <div class="space-y-6">
                    {{-- Base URL --}}
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">BASE URL</div>
                        <div class="p-3 bg-slate-900 rounded-lg">
                            <code class="text-sm text-emerald-400 font-mono">{{ url('/api/v1') }}</code>
                        </div>
                    </div>

                    {{-- Authentication --}}
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">AUTENTIKASI</div>
                        <p class="text-sm text-slate-600 m-0 mb-3">Sertakan API token di header <code class="px-1.5 py-0.5 bg-slate-100 rounded text-xs font-mono text-brand">X-API-TOKEN</code> pada setiap request.</p>
                        <div class="p-3 bg-slate-900 rounded-lg overflow-x-auto">
                            <pre class="m-0 text-sm text-slate-300 font-mono whitespace-pre"><span class="text-amber-400">curl</span> -H <span class="text-emerald-400">"X-API-TOKEN: your-token-here"</span> \
     {{ url('/api/v1/jobs') }}</pre>
                        </div>
                    </div>

                    {{-- Endpoints --}}
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">ENDPOINTS</div>
                        <div class="space-y-3">
                            {{-- GET /jobs --}}
                            <div class="border border-slate-200 rounded-xl overflow-hidden">
                                <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 border-b border-slate-200">
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-md font-mono">GET</span>
                                    <code class="text-sm text-slate-800 font-mono">/api/v1/jobs</code>
                                    <span class="text-xs text-slate-400 ml-auto">Daftar lowongan</span>
                                </div>
                                <div class="p-4">
                                    <p class="text-sm text-slate-600 m-0 mb-3">Menampilkan daftar lowongan kerja yang dipublikasikan (paginated).</p>
                                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">QUERY PARAMETERS</div>
                                    <div class="overflow-hidden rounded-lg border border-slate-200">
                                        <table class="w-full text-sm">
                                            <thead class="bg-slate-50">
                                                <tr>
                                                    <th class="px-3 py-2 text-left font-semibold text-slate-600 text-xs">Parameter</th>
                                                    <th class="px-3 py-2 text-left font-semibold text-slate-600 text-xs">Tipe</th>
                                                    <th class="px-3 py-2 text-left font-semibold text-slate-600 text-xs">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="border-t border-slate-100">
                                                    <td class="px-3 py-2"><code class="text-xs font-mono text-brand bg-blue-50 px-1.5 py-0.5 rounded">search</code></td>
                                                    <td class="px-3 py-2 text-slate-500 text-xs">string</td>
                                                    <td class="px-3 py-2 text-slate-600 text-xs">Filter berdasarkan judul</td>
                                                </tr>
                                                <tr class="border-t border-slate-100">
                                                    <td class="px-3 py-2"><code class="text-xs font-mono text-brand bg-blue-50 px-1.5 py-0.5 rounded">type</code></td>
                                                    <td class="px-3 py-2 text-slate-500 text-xs">string</td>
                                                    <td class="px-3 py-2 text-slate-600 text-xs">Filter tipe: permanent, contract, internship, freelance</td>
                                                </tr>
                                                <tr class="border-t border-slate-100">
                                                    <td class="px-3 py-2"><code class="text-xs font-mono text-brand bg-blue-50 px-1.5 py-0.5 rounded">per_page</code></td>
                                                    <td class="px-3 py-2 text-slate-500 text-xs">integer</td>
                                                    <td class="px-3 py-2 text-slate-600 text-xs">Jumlah per halaman (default: 15)</td>
                                                </tr>
                                                <tr class="border-t border-slate-100">
                                                    <td class="px-3 py-2"><code class="text-xs font-mono text-brand bg-blue-50 px-1.5 py-0.5 rounded">page</code></td>
                                                    <td class="px-3 py-2 text-slate-500 text-xs">integer</td>
                                                    <td class="px-3 py-2 text-slate-600 text-xs">Nomor halaman</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- GET /jobs/{slug} --}}
                            <div class="border border-slate-200 rounded-xl overflow-hidden">
                                <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 border-b border-slate-200">
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-md font-mono">GET</span>
                                    <code class="text-sm text-slate-800 font-mono">/api/v1/jobs/{slug}</code>
                                    <span class="text-xs text-slate-400 ml-auto">Detail lowongan</span>
                                </div>
                                <div class="p-4">
                                    <p class="text-sm text-slate-600 m-0 mb-3">Menampilkan detail satu lowongan berdasarkan slug.</p>
                                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">CONTOH REQUEST</div>
                                    <div class="p-3 bg-slate-900 rounded-lg overflow-x-auto">
                                        <pre class="m-0 text-sm text-slate-300 font-mono whitespace-pre"><span class="text-amber-400">curl</span> -H <span class="text-emerald-400">"X-API-TOKEN: your-token"</span> \
     {{ url('/api/v1/jobs/freelance-graphic-designer') }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Response Example --}}
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">CONTOH RESPONSE</div>
                        <div class="p-3 bg-slate-900 rounded-lg overflow-x-auto">
                            <pre class="m-0 text-sm text-slate-300 font-mono whitespace-pre">{
  <span class="text-sky-400">"data"</span>: [
    {
      <span class="text-sky-400">"id"</span>: <span class="text-amber-400">1</span>,
      <span class="text-sky-400">"title"</span>: <span class="text-emerald-400">"Freelance Graphic Designer"</span>,
      <span class="text-sky-400">"slug"</span>: <span class="text-emerald-400">"freelance-graphic-designer"</span>,
      <span class="text-sky-400">"department"</span>: <span class="text-emerald-400">"Creative"</span>,
      <span class="text-sky-400">"employment_type"</span>: <span class="text-emerald-400">"freelance"</span>,
      <span class="text-sky-400">"salary_range"</span>: <span class="text-emerald-400">"Rp 5.000.000 - Rp 8.000.000"</span>,
      <span class="text-sky-400">"close_date"</span>: <span class="text-emerald-400">"2026-05-30"</span>
    }
  ],
  <span class="text-sky-400">"success"</span>: <span class="text-amber-400">true</span>,
  <span class="text-sky-400">"links"</span>: { ... },
  <span class="text-sky-400">"meta"</span>: { ... }
}</pre>
                        </div>
                    </div>

                    {{-- Rate Limiting --}}
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">RATE LIMITING</div>
                        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span class="text-sm text-blue-700">Maksimum <strong>60 request per menit</strong> per IP address.</span>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Right Side — Quick Info --}}
        <div class="space-y-6">
            {{-- Security Info --}}
            <x-ui.card title="🔐 Keamanan">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 mt-0.5">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-800">SHA-256 Hashing</div>
                            <div class="text-xs text-slate-500 mt-0.5">Token disimpan sebagai hash, bukan plaintext</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 mt-0.5">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-800">Header-Only Auth</div>
                            <div class="text-xs text-slate-500 mt-0.5">Token hanya via X-API-TOKEN header</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 mt-0.5">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-800">Rate Limited</div>
                            <div class="text-xs text-slate-500 mt-0.5">60 request/menit per IP</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 mt-0.5">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-800">CORS Protected</div>
                            <div class="text-xs text-slate-500 mt-0.5">Cross-origin terkelola via config</div>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Integration Guide --}}
            <x-ui.card title="📖 Panduan Integrasi">
                <div class="space-y-4 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-brand/10 text-brand flex items-center justify-center shrink-0 text-xs font-bold">1</div>
                        <div class="text-slate-600">Generate API token dari halaman ini</div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-brand/10 text-brand flex items-center justify-center shrink-0 text-xs font-bold">2</div>
                        <div class="text-slate-600">Salin dan simpan token dengan aman</div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-brand/10 text-brand flex items-center justify-center shrink-0 text-xs font-bold">3</div>
                        <div class="text-slate-600">Gunakan token di header <code class="text-xs bg-slate-100 px-1 rounded">X-API-TOKEN</code></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-brand/10 text-brand flex items-center justify-center shrink-0 text-xs font-bold">4</div>
                        <div class="text-slate-600">Akses endpoint <code class="text-xs bg-slate-100 px-1 rounded">/api/v1/jobs</code> dari website Anda</div>
                    </div>
                </div>
            </x-ui.card>

            {{-- JavaScript Example --}}
            <x-ui.card title="💻 Contoh JavaScript">
                <div class="p-3 bg-slate-900 rounded-lg overflow-x-auto">
                    <pre class="m-0 text-xs text-slate-300 font-mono whitespace-pre leading-relaxed"><span class="text-violet-400">const</span> response = <span class="text-violet-400">await</span> <span class="text-amber-400">fetch</span>(
  <span class="text-emerald-400">'{{ url('/api/v1/jobs') }}'</span>,
  {
    <span class="text-sky-400">headers</span>: {
      <span class="text-emerald-400">'X-API-TOKEN'</span>: token
    }
  }
);
<span class="text-violet-400">const</span> { data } = <span class="text-violet-400">await</span> response.<span class="text-amber-400">json</span>();</pre>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
