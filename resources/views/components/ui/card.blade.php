@props([
    'title' => null,
    'padding' => true,
])

<div class="bg-white rounded-2xl border border-black/[0.06] shadow-[0_1px_3px_rgba(0,0,0,0.04)] overflow-hidden transition-shadow duration-200" {{ $attributes }}>
    @if($title || isset($header))
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            @if($title)
                <h3 class="m-0 text-base font-semibold text-slate-900">{{ $title }}</h3>
            @endif
            @isset($header)
                {{ $header }}
            @endisset
        </div>
    @endif

    <div @if($padding) class="p-6" @endif>
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="px-6 py-4 border-t border-slate-100 bg-[#FAFBFC]">
            {{ $footer }}
        </div>
    @endisset
</div>
