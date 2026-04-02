@props([
    'striped' => true,
    'hoverable' => true,
])

<div class="bg-white rounded-2xl border border-black/[0.06] shadow-[0_1px_3px_rgba(0,0,0,0.04)] overflow-hidden">
    {{-- Header --}}
    @isset($header)
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            {{ $header }}
        </div>
    @endisset

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-sm" {{ $attributes }}>
            @isset($head)
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        {{ $head }}
                    </tr>
                </thead>
            @endisset
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    @isset($footer)
        <div class="px-6 py-3 border-t border-slate-100 bg-[#FAFBFC]">
            {{ $footer }}
        </div>
    @endisset
</div>

<style>
    table th {
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 600;
        color: #64748B;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    table td {
        padding: 14px 16px;
        color: #334155;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
    }
    @if($hoverable)
    table tbody tr:hover {
        background: #F8FAFC;
    }
    @endif
    @if($striped)
    table tbody tr:nth-child(even) {
        background: #FAFBFC;
    }
    @endif
</style>
