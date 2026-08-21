@props(['type' => 'success'])

@php
    $types = [
        'success' => ['bg-brutal-neon text-black', 'check'],
        'error' => ['bg-brutal-pink text-white', 'circle-x'],
        'warning' => ['bg-brutal-yellow text-black', 'triangle-alert'],
        'info' => ['bg-brutal-blue text-white', 'info'],
    ];
    [$classes, $iconName] = $types[$type] ?? $types['info'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3 border-3 border-black p-4 font-mono text-xs font-bold uppercase tracking-wider shadow-brutal ' . $classes]) }}>
    <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center border border-black bg-black text-white"><x-icon :name="$iconName" class="h-3.5 w-3.5" /></span>
    <div class="flex-1 leading-relaxed">{{ $slot }}</div>
</div>

