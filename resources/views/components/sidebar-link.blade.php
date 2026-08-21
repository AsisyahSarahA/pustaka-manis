@props([
    'active' => false,
    'icon' => null,
])

<a
    {{ $attributes->merge(['class' => 'group relative flex items-center gap-3 border-2 px-3.5 py-2.5 text-xs font-bold uppercase tracking-wider transition-transform duration-75 ease-linear ' . ($active ? 'border-black bg-brutal-yellow text-black shadow-brutal-sm' : 'border-transparent text-black hover:border-black hover:bg-white hover:translate-x-1')]) }}
>
    @if ($icon)
        <span class="h-5 w-5 shrink-0 text-black">{!! $icon !!}</span>
    @endif
    <span class="truncate">{{ $slot }}</span>
    @if ($active)
        <span class="ml-auto font-mono text-[10px] font-black text-black">◄</span>
    @endif
</a>

