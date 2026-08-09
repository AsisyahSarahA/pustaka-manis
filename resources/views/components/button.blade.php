@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'icon' => null,
])

@php
    $variants = [
        'primary' => 'bg-azure-soft text-navy-deep shadow-tactile hover:bg-azure-soft/90',
        'secondary' => 'bg-white/10 text-pearl border border-white/20 shadow-glass hover:bg-white/15',
        'danger' => 'bg-danger-red text-white shadow-tactile hover:bg-danger-red/90',
        'amber' => 'bg-amber-warm text-navy-deep shadow-tactile hover:bg-amber-warm/90',
    ];
    $classes = 'btn-skeuo inline-flex items-center justify-center gap-2 rounded-pill px-5 py-2.5 text-sm font-semibold transition-transform duration-150 active:translate-y-px disabled:opacity-50 disabled:pointer-events-none ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<span class="h-4 w-4">{!! $icon !!}</span>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<span class="h-4 w-4">{!! $icon !!}</span>@endif
        {{ $slot }}
    </button>
@endif
