@props([
    'id' => 'modal-' . uniqid(),
    'maxWidth' => '540px',
    'title' => null,
])

<div
    x-data="{ show: false }"
    x-on:open-modal-{{ $id }}.window="show = true"
    x-on:close-modal-{{ $id }}.window="show = false"
    x-on:keydown.escape.window="show = false"
    {{ $attributes }}
>
    {{-- Trigger --}}
    @isset($trigger)
        <div x-on:click="show = true">
            {{ $trigger }}
        </div>
    @endisset

    {{-- Overlay + Modal --}}
    <template x-teleport="body">
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-6"
            x-on:click.self="show = false"
        >
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="bg-white rounded-2xl shadow-[0_32px_64px_rgba(0,0,0,0.2)] w-full max-h-[85vh] overflow-y-auto"
                style="max-width: {{ $maxWidth }};"
            >
                {{-- Header --}}
                @if($title)
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="m-0 text-[17px] font-semibold text-slate-900">{{ $title }}</h3>
                        <button
                            x-on:click="show = false"
                            class="p-1.5 border-none bg-slate-100 rounded-lg cursor-pointer text-slate-500 transition-all duration-200 hover:bg-slate-200"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                @endif

                {{-- Body --}}
                <div class="p-6">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                @isset($footer)
                    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        {{ $footer }}
                    </div>
                @endisset
            </div>
        </div>
    </template>
</div>
