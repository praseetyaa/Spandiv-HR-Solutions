@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'hint' => null,
])

<div class="mb-4">
    @if($label)
        <label
            for="{{ $name }}"
            class="block mb-1.5 text-[13px] font-semibold text-slate-700"
        >
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        class="form-input w-full py-2.5 px-3.5 border rounded-[10px] text-sm text-slate-900 font-[Inter,sans-serif] transition-all duration-200 outline-none box-border
            {{ $errors->has($name) ? 'border-red-300' : 'border-slate-200' }}
            {{ $disabled ? 'bg-slate-50' : 'bg-white' }}"
        {{ $attributes }}
    >

    @if($hint && !$errors->has($name))
        <p class="mt-1 mb-0 text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
