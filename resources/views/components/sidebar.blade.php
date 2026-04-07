{{-- Sidebar Component --}}
@php
    $menuGroups = [
        [
            'label' => null,
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
            ],
        ],
        [
            'label' => 'KARYAWAN',
            'items' => [
                ['label' => 'Data Karyawan', 'route' => 'employees.index', 'icon' => 'users'],
                ['label' => 'Departemen', 'route' => 'employees.departments', 'icon' => 'grid'],
                ['label' => 'Jabatan', 'route' => 'employees.positions', 'icon' => 'briefcase'],
            ],
        ],
        [
            'label' => 'KEHADIRAN',
            'items' => [
                ['label' => 'Absensi', 'route' => 'attendance.index', 'icon' => 'clock'],
                ['label' => 'Cuti & Izin', 'route' => 'attendance.leave', 'icon' => 'calendar'],
                ['label' => 'Lembur', 'route' => 'attendance.overtime', 'icon' => 'timer'],
            ],
        ],
        [
            'label' => 'PAYROLL',
            'items' => [
                ['label' => 'Penggajian', 'route' => 'payroll.index', 'icon' => 'wallet'],
                ['label' => 'Komponen Gaji', 'route' => 'payroll.components', 'icon' => 'sliders'],
                ['label' => 'Bonus', 'route' => 'payroll.bonus', 'icon' => 'gift'],
            ],
        ],
        [
            'label' => 'REKRUTMEN',
            'items' => [
                ['label' => 'Lowongan', 'route' => 'recruitment.postings', 'icon' => 'megaphone'],
                ['label' => 'Kandidat', 'route' => 'recruitment.candidates', 'icon' => 'user-plus'],
                ['label' => 'Onboarding', 'route' => 'recruitment.onboarding', 'icon' => 'briefcase'],
            ],
        ],
        [
            'label' => 'TES PSIKOLOGI',
            'items' => [
                ['label' => 'Bank Tes', 'route' => 'psych-test.tests', 'icon' => 'brain'],
                ['label' => 'Penugasan', 'route' => 'psych-test.assignments', 'icon' => 'clipboard'],
                ['label' => 'Hasil Tes', 'route' => 'psych-test.results', 'icon' => 'chart'],
            ],
        ],
        [
            'label' => 'PENGEMBANGAN',
            'items' => [
                ['label' => 'Performa', 'route' => 'performance.cycles', 'icon' => 'chart'],
                ['label' => 'Goal / KPI', 'route' => 'performance.goals', 'icon' => 'target'],
                ['label' => 'Talent 9-Box', 'route' => 'talent.nine-box', 'icon' => 'star'],
                ['label' => 'IDP', 'route' => 'idp.index', 'icon' => 'book'],
                ['label' => 'Kursus', 'route' => 'learning.courses', 'icon' => 'book'],
                ['label' => 'Training', 'route' => 'learning.training', 'icon' => 'users'],
                ['label' => 'Sertifikasi', 'route' => 'learning.certifications', 'icon' => 'shield'],
            ],
        ],
        [
            'label' => 'BENEFIT',
            'items' => [
                ['label' => 'Benefit', 'route' => 'benefit.index', 'icon' => 'heart'],
                ['label' => 'Expense', 'route' => 'expense.index', 'icon' => 'wallet'],
                ['label' => 'Pinjaman', 'route' => 'loans.index', 'icon' => 'wallet'],
            ],
        ],
        [
            'label' => 'COMPLIANCE',
            'items' => [
                ['label' => 'Kebijakan', 'route' => 'compliance.policies', 'icon' => 'book'],
                ['label' => 'Disipliner', 'route' => 'compliance.disciplinary', 'icon' => 'shield'],
                ['label' => 'Keluhan', 'route' => 'compliance.grievances', 'icon' => 'megaphone'],
            ],
        ],
        [
            'label' => 'ENGAGEMENT',
            'items' => [
                ['label' => 'Survei', 'route' => 'engagement.surveys', 'icon' => 'chart'],
                ['label' => 'Rekognisi', 'route' => 'engagement.recognition', 'icon' => 'star'],
                ['label' => 'Pengumuman', 'route' => 'engagement.announcements', 'icon' => 'bell'],
            ],
        ],
        [
            'label' => 'PENGATURAN',
            'items' => [
                ['label' => 'Umum', 'route' => 'settings.general', 'icon' => 'settings'],
                ['label' => 'Notifikasi', 'route' => 'settings.notifications', 'icon' => 'bell'],
                ['label' => 'Audit Log', 'route' => 'settings.audit-log', 'icon' => 'search'],
                ['label' => 'API', 'route' => 'settings.api', 'icon' => 'key'],
            ],
        ],
    ];

    $icons = [
        'dashboard' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
        'briefcase' => '<rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'calendar' => '<rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>',
        'timer' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 10"/>',
        'wallet' => '<rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/><path d="M16 14h2"/>',
        'sliders' => '<line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/><line x1="2" x2="6" y1="14" y2="14"/><line x1="10" x2="14" y1="8" y2="8"/><line x1="18" x2="22" y1="16" y2="16"/>',
        'gift' => '<polyline points="20 12 20 22 4 22 4 12"/><rect width="20" height="5" x="2" y="7"/><line x1="12" x2="12" y1="22" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>',
        'megaphone' => '<path d="m3 11 18-5v12L3 13v-2z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>',
        'user-plus' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/>',
        'brain' => '<path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96.44 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 1.98-3A2.5 2.5 0 0 1 9.5 2z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-1.98-3A2.5 2.5 0 0 0 14.5 2z"/>',
        'chart' => '<line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/>',
        'book' => '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/>',
        'star' => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        'heart' => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'bell' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
        'target' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c.26.604.852.997 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
        'search' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
        'clipboard' => '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>',
        'key' => '<path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>',
    ];
@endphp

<aside
    class="hr-sidebar fixed top-0 left-0 bottom-0 z-50 flex flex-col overflow-hidden"
    x-bind:class="$store.sidebar.open ? 'is-open w-[260px]' : 'is-collapsed w-[72px]'"
>
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/[0.06] min-h-[72px] shrink-0">
        <div class="sidebar-logo-icon shrink-0 w-9 h-9 rounded-[10px] flex items-center justify-center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div x-show="$store.sidebar.open" x-transition.opacity.duration.200ms class="overflow-hidden whitespace-nowrap">
            <div class="text-white font-bold text-[15px] leading-tight">HR Solutions</div>
            <div class="text-white/40 text-[11px]">by Spandiv</div>
        </div>
    </div>

    {{-- Menu --}}
    <nav class="sidebar-nav flex-1 overflow-y-auto py-3 px-2">
        @foreach ($menuGroups as $group)
            @if ($group['label'])
                <div x-show="$store.sidebar.open" class="px-3 pt-4 pb-1.5 text-white/30 text-[11px] font-semibold tracking-[1.5px] uppercase">
                    {{ $group['label'] }}
                </div>
                <div x-show="!$store.sidebar.open" class="h-px bg-white/[0.06] mx-3 my-2"></div>
            @endif

            @foreach ($group['items'] as $item)
                @php
                    $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                    $iconSvg = $icons[$item['icon']] ?? '';
                @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] mb-0.5 text-white/55 no-underline text-sm font-normal transition-all duration-200 relative whitespace-nowrap overflow-hidden hover:bg-white/5 hover:text-white/85 {{ $isActive ? 'sidebar-item active !text-white !bg-brand/25 !font-semibold' : '' }}"
                    @if(!$isActive)
                        title="{{ $item['label'] }}"
                    @endif
                >
                    <div class="shrink-0 w-5 h-5 flex items-center justify-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            {!! $iconSvg !!}
                        </svg>
                    </div>
                    <span x-show="$store.sidebar.open" x-transition.opacity.duration.200ms class="whitespace-nowrap overflow-hidden transition-opacity duration-200">
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        @endforeach
    </nav>

    {{-- Toggle Button --}}
    <div class="px-2 py-3 border-t border-white/[0.06] shrink-0">
        <button
            x-on:click="$store.sidebar.toggle()"
            class="w-full px-3 py-2.5 border-none rounded-[10px] bg-white/[0.04] text-white/40 cursor-pointer flex items-center justify-center gap-2 text-[13px] font-[inherit] transition-all duration-200 hover:bg-white/[0.08] hover:text-white/70"
        >
            <svg
                x-bind:style="$store.sidebar.open ? '' : 'transform: rotate(180deg)'"
                width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="transition-transform duration-300"
            >
                <path d="m11 17-5-5 5-5"/><path d="m18 17-5-5 5-5"/>
            </svg>
            <span x-show="$store.sidebar.open" class="whitespace-nowrap">Collapse</span>
        </button>
    </div>
</aside>
