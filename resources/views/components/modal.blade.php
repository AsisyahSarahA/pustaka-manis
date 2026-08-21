@props(['title' => null, 'maxWidth' => 'max-w-lg', 'id' => 'modal'])

<div
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    x-cloak
>
    {{ $trigger }}

    <div
        x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 brutal-backdrop"
        @click="open = false"
    >
        <div
            class="w-full {{ $maxWidth }} border-4 border-black bg-white p-0 shadow-brutal-xl text-black"
            @click.stop
            x-trap.noscroll="open"
        >
            @if ($title)
                <div class="flex items-center justify-between border-b-4 border-black bg-black px-5 py-3 text-white">
                    <h3 class="font-heading text-sm font-black uppercase tracking-wider text-brutal-yellow">
                        [SYSTEM WINDOW] :: {{ $title }}
                    </h3>
                    <button @click="open = false" class="btn-skeuo border-2 border-white bg-brutal-pink px-2 py-0.5 font-mono text-xs font-black text-white hover:bg-white hover:text-black">
                        [X]
                    </button>
                </div>
            @endif
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

