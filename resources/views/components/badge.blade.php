@props([
    'variant' => 'neutral',
    'dot' => false,
])

@php
    $variants = [
        'neutral' => 'bg-brutal-input text-black',
        'azure' => 'bg-brutal-neon text-black',
        'azure-light' => 'bg-brutal-blue text-white',
        'amber' => 'bg-brutal-yellow text-black',
        'red' => 'bg-brutal-pink text-white',
        'blue' => 'bg-brutal-blue text-white',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 border-2 border-black px-2.5 py-0.5 font-mono text-[10px] font-black uppercase tracking-wider shadow-brutal-sm ' . ($variants[$variant] ?? $variants['neutral'])]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 bg-current"></span>
    @endif
    {{ $slot }}
</span>

