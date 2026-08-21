@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-5',
])

<div {{ $attributes->merge(['class' => 'brutal-card']) }}>
    @if ($title)
        <div class="mb-4 flex items-center justify-between gap-4 border-b-3 border-black pb-3">
            <div>
                @if ($title)
                    <h3 class="font-heading text-base font-black uppercase tracking-wide text-black">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="mt-0.5 font-mono text-xs font-bold uppercase tracking-wider text-black/60">{{ $subtitle }}</p>
                @endif
            </div>
            {{ $actions ?? '' }}
        </div>
    @endif
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>

