@props([
    'type' => 'default',
    'size' => 'sm',
])

@php
    $colorClasses = [
        'default'  => 'bg-slate-100 text-slate-600',
        'success'  => 'bg-green-100 text-green-800',
        'warning'  => 'bg-amber-100 text-amber-800',
        'danger'   => 'bg-red-100 text-red-800',
        'info'     => 'bg-blue-100 text-blue-800',
        'brand'    => 'bg-brand-light text-brand-dark',
        'active'   => 'bg-green-100 text-green-800',
        'inactive' => 'bg-red-100 text-red-800',
        'pending'  => 'bg-amber-100 text-amber-800',
    ];

    $sizeClasses = [
        'xs' => 'px-2 py-0.5 text-[11px]',
        'sm' => 'px-2.5 py-0.5 text-xs',
        'md' => 'px-3.5 py-1 text-[13px]',
    ];

    $classes = ($colorClasses[$type] ?? $colorClasses['default']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['sm']);
@endphp

<span class="inline-flex items-center gap-1 rounded-full font-semibold tracking-wide {{ $classes }}" {{ $attributes }}>
    {{ $slot }}
</span>
