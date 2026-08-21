@props([
    'type' => 'text',
    'label' => null,
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'autofocus' => false,
    'autocomplete' => 'off',
    'icon' => null,
    'error' => null,
])

<div class="w-full">
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block font-mono text-xs font-black uppercase tracking-widest text-black">
            ❯ {{ $label }} @if ($required)<span class="text-brutal-pink">*</span>@endif
        </label>
    @endif
    <div class="relative">
        @if ($icon)
            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-black">{!! $icon !!}</span>
        @endif
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            @if ($autofocus) autofocus @endif
            autocomplete="{{ $autocomplete }}"
            {{ $attributes->merge(['class' => 'input-debossed w-full border-3 border-black px-4 py-2.5 text-sm font-semibold text-black placeholder-black/50 transition-shadow duration-75 ' . ($icon ? 'pl-10' : '') . ($error ? ' border-brutal-pink bg-brutal-pink/10' : '')]) }}
        />
    </div>
    @if ($error)
        <p class="mt-1 font-mono text-[11px] font-bold text-brutal-pink uppercase">⚠ {{ $error }}</p>
    @endif
</div>

