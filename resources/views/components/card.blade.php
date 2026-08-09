@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6',
])

<div {{ $attributes->merge(['class' => 'glass-panel rounded-4xl']) }}>
    @if ($title)
        <div class="mb-5 flex items-center justify-between gap-4">
            <div>
                @if ($title)
                    <h3 class="text-lg font-bold text-pearl">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="mt-0.5 text-sm text-pearl/50">{{ $subtitle }}</p>
                @endif
            </div>
            {{ $actions ?? '' }}
        </div>
    @endif
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
