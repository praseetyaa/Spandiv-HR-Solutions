@props([
    'type' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
])

@php
    $typeClasses = [
        'primary'   => 'bg-gradient-to-br from-brand to-[#3468B8] text-white border-none',
        'secondary' => 'bg-slate-100 text-slate-700 border border-slate-200',
        'danger'    => 'bg-danger text-white border-none',
        'success'   => 'bg-success text-white border-none',
        'ghost'     => 'bg-transparent text-brand border-none',
        'outline'   => 'bg-transparent text-brand border border-brand',
    ];

    $sizeClasses = [
        'sm' => 'px-3.5 py-1.5 text-[13px] rounded-lg',
        'md' => 'px-5 py-2.5 text-sm rounded-[10px]',
        'lg' => 'px-7 py-3 text-[15px] rounded-xl',
    ];

    $classes = 'btn-lift inline-flex items-center gap-2 font-semibold font-[inherit] cursor-pointer transition-all duration-200 no-underline whitespace-nowrap '
        . ($typeClasses[$type] ?? $typeClasses['primary']) . ' '
        . ($sizeClasses[$size] ?? $sizeClasses['md']);

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    class="{{ $classes }}"
    {{ $attributes }}
>
    {{ $slot }}
</{{ $tag }}>
