@props(['type' => 'success'])

@php
    $types = [
        'success' => ['bg-success-soft text-success-green', 'check'],
        'error' => ['bg-danger-soft text-danger-red', 'circle-x'],
        'warning' => ['bg-amber-warm/20 text-amber-warm', 'triangle-alert'],
        'info' => ['bg-white/10 text-azure-soft', 'info'],
    ];
    [$classes, $iconName] = $types[$type] ?? $types['info'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-2xl border border-white/10 px-4 py-3 text-sm ' . $classes]) }}>
    <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white/20"><x-icon :name="$iconName" class="h-3.5 w-3.5" /></span>
    <div class="flex-1">{{ $slot }}</div>
</div>
