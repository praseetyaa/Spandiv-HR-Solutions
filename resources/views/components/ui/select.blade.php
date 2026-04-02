@props([
    'label' => '',
    'name' => '',
    'placeholder' => 'Pilih...',
    'options' => [],
    'selected' => null,
    'required' => false,
    'disabled' => false,
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

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        class="form-input w-full py-2.5 pl-3.5 pr-10 border rounded-[10px] text-sm text-slate-900 font-[Inter,sans-serif] transition-all duration-200 outline-none box-border appearance-none bg-no-repeat bg-[right_12px_center] bg-[length:18px]
            {{ $errors->has($name) ? 'border-red-300' : 'border-slate-200' }}
            {{ $disabled ? 'bg-slate-50' : 'bg-white' }}"
        style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2394A3B8%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3e%3cpolyline points=%226 9 12 15 18 9%22/%3e%3c/svg%3e');"
        {{ $attributes }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
        {{ $slot }}
    </select>

    @error($name)
        <p class="mt-1 mb-0 text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
