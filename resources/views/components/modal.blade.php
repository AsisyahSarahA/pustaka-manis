@props(['title' => null, 'maxWidth' => 'max-w-lg', 'id' => 'modal'])

<div
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    x-cloak
>
    {{ $trigger }}

    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-navy-deep/70 p-4 backdrop-blur-sm"
        @click="open = false"
    >
        <div
            class="glass-panel w-full {{ $maxWidth }} rounded-5xl p-6"
            @click.stop
            x-trap.noscroll="open"
        >
            @if ($title)
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-pearl">{{ $title }}</h3>
                    <button @click="open = false" class="rounded-xl p-2 text-pearl/50 transition hover:bg-white/5 hover:text-pearl"><x-icon name="x" class="h-4 w-4" /></button>
                </div>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
