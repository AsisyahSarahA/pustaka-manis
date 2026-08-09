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
        <label for="{{ $name }}" class="mb-2 block text-sm font-medium text-pearl/80">{{ $label }}</label>
    @endif
    <div class="relative">
        @if ($icon)
            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-pearl/50">{!! $icon !!}</span>
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
            {{ $attributes->merge(['class' => 'input-debossed w-full rounded-pill border-0 px-5 py-3 text-sm transition-all duration-200 ' . ($icon ? 'pl-12' : '') . ($error ? ' ring-2 ring-danger-red/60' : '')]) }}
        />
    </div>
    @if ($error)
        <p class="mt-1 text-xs text-danger-red">{{ $error }}</p>
    @endif
</div>
