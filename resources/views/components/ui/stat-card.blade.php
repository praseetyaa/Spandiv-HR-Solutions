@props([
    'label' => '',
    'value' => '0',
    'icon' => 'users',
    'color' => 'brand',
    'trend' => null,
    'trendLabel' => '',
])

@php
    $colors = [
        'brand'   => ['icon' => '#2B5BA8', 'iconBg' => 'rgba(43,91,168,0.12)'],
        'success' => ['icon' => '#16A34A', 'iconBg' => 'rgba(22,163,74,0.12)'],
        'warning' => ['icon' => '#F59E0B', 'iconBg' => 'rgba(245,158,11,0.12)'],
        'danger'  => ['icon' => '#DC2626', 'iconBg' => 'rgba(220,38,38,0.12)'],
        'info'    => ['icon' => '#0EA5E9', 'iconBg' => 'rgba(14,165,233,0.12)'],
    ];

    $c = $colors[$color] ?? $colors['brand'];

    $icons = [
        'users'       => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'clock'       => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'calendar'    => '<rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>',
        'grid'        => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
        'building'    => '<rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/>',
        'credit-card' => '<rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>',
        'package'     => '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
        'wallet'      => '<rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/><path d="M16 14h2"/>',
    ];
    $iconSvg = $icons[$icon] ?? $icons['users'];
@endphp

<div class="stat-card bg-white rounded-2xl border border-black/[0.06] shadow-[0_1px_3px_rgba(0,0,0,0.04)] p-6 transition-all duration-300 cursor-default">
    <div class="flex items-start justify-between mb-4">
        <div
            class="w-12 h-12 rounded-xl flex items-center justify-center"
            style="background: {{ $c['iconBg'] }};"
        >
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="{{ $c['icon'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                {!! $iconSvg !!}
            </svg>
        </div>

        @if($trend !== null)
            <div class="flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full {{ $trend >= 0 ? 'text-green-700 bg-green-50' : 'text-red-700 bg-red-50' }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                    @if($trend < 0) class="rotate-180" @endif
                >
                    <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
                </svg>
                {{ abs($trend) }}%
            </div>
        @endif
    </div>

    <div class="text-[28px] font-extrabold text-slate-900 leading-none mb-1.5">
        {{ $value }}
    </div>
    <div class="text-[13px] text-slate-500 font-medium">
        {{ $label }}
        @if($trendLabel)
            <span class="text-slate-400 font-normal">· {{ $trendLabel }}</span>
        @endif
    </div>
</div>
