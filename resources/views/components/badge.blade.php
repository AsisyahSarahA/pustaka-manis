@props([
    'variant' => 'neutral',
    'dot' => false,
])

@php
    $variants = [
        'neutral' => 'bg-white/10 text-pearl',
        'azure' => 'bg-success-soft text-success-green',
        'azure-light' => 'bg-white/10 text-azure-soft',
        'amber' => 'bg-amber-warm/20 text-amber-warm',
        'red' => 'bg-danger-soft text-danger-red',
        'blue' => 'bg-white/10 text-sky-300',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-pill px-3 py-1 text-xs font-semibold ' . ($variants[$variant] ?? $variants['neutral'])]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
    @endif
    {{ $slot }}
</span>
