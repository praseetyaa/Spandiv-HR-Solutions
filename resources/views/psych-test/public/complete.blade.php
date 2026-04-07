@extends('layouts.exam', ['examTitle' => 'Tes Selesai'])

@section('content')
    <div class="max-w-lg mx-auto text-center">
        {{-- Success Icon --}}
        <div class="w-20 h-20 mx-auto rounded-full bg-emerald-50 flex items-center justify-center mb-6">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <path d="m9 11 3 3L22 4"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-slate-800 mb-2">Tes Berhasil Dikirim! 🎉</h1>
        <p class="text-slate-500 text-sm leading-relaxed mb-8">
            Terima kasih, <strong class="text-slate-700">{{ $candidate->name }}</strong>.
            Jawaban Anda telah tersimpan dan sedang diproses.
            Tim HR akan menghubungi Anda mengenai hasil tes.
        </p>

        {{-- Info Cards --}}
        <div class="bg-white rounded-xl border border-slate-100 p-6 text-left mb-6">
            <h3 class="text-sm font-bold text-slate-700 mb-3">Informasi Penting</h3>
            <ul class="space-y-2 text-sm text-slate-500">
                <li class="flex items-start gap-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" class="mt-0.5 shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                    Jawaban Anda sudah disimpan secara otomatis.
                </li>
                <li class="flex items-start gap-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" class="mt-0.5 shrink-0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Hasil tes akan dievaluasi dalam 1-3 hari kerja.
                </li>
                <li class="flex items-start gap-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2B5BA8" stroke-width="2" class="mt-0.5 shrink-0"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2Z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Notifikasi hasil akan dikirim via email.
                </li>
            </ul>
        </div>

        <p class="text-xs text-slate-400">
            Anda dapat menutup halaman ini. Terima kasih atas partisipasi Anda.
        </p>
    </div>
@endsection
