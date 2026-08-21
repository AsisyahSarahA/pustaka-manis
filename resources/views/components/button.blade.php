@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'icon' => null,
])

@php
    $variants = [
        'primary' => 'bg-brutal-neon text-black',
        'secondary' => 'bg-white text-black',
        'danger' => 'bg-brutal-pink text-white',
        'amber' => 'bg-brutal-yellow text-black',
        'yellow' => 'bg-brutal-yellow text-black',
        'blue' => 'bg-brutal-blue text-white',
    ];
    $classes = 'btn-brutal inline-flex items-center justify-center gap-2 border-3 border-black px-5 py-2.5 text-xs font-extrabold uppercase tracking-wider disabled:opacity-50 disabled:pointer-events-none ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<span class="h-4 w-4 shrink-0">{!! $icon !!}</span>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<span class="h-4 w-4 shrink-0">{!! $icon !!}</span>@endif
        {{ $slot }}
    </button>
@endif

