@props([
    'active' => false,
    'icon' => null,
])

<a
    {{ $attributes->merge(['class' => 'group relative flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200 ' . ($active ? 'bg-azure-soft/15 text-azure-soft shadow-[0_0_20px_rgba(151,221,233,0.15)]' : 'text-pearl/60 hover:bg-white/5 hover:text-pearl')]) }}
>
    @if ($icon)
        <span class="h-5 w-5 shrink-0">{!! $icon !!}</span>
    @endif
    <span class="truncate">{{ $slot }}</span>
    @if ($active)
        <span class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-gradient-to-b from-azure-light to-azure-soft shadow-[0_0_12px_rgba(151,221,233,0.8)]"></span>
    @endif
</a>
