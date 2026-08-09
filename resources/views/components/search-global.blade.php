@props(['placeholder' => 'Cari buku, anggota, atau kode...'])

<div
    x-data="globalSearch()"
    x-init="init()"
    class="relative w-full max-w-md"
    @keydown.escape.window="open = false"
>
    <div class="relative">
        <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-pearl/50">
            <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='h-4 w-4'><path stroke-linecap='round' stroke-linejoin='round' d='M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z'/></svg>
        </span>
        <input
            type="text"
            x-model="query"
            @input.debounce.300ms="search()"
            @focus="open = query.trim().length >= 2"
            @keydown.arrow-down.prevent="move(1)"
            @keydown.arrow-up.prevent="move(-1)"
            @keydown.enter.prevent="go()"
            placeholder="{{ $placeholder }}"
            class="input-debossed w-full rounded-pill border-0 py-2.5 pl-11 pr-4 text-sm"
            autocomplete="off"
        />
    </div>

    {{-- Dropdown hasil --}}
    <div
        x-show="open && loading"
        x-cloak
        class="absolute left-0 right-0 top-full z-50 mt-2 rounded-2xl bg-navy-soft/95 p-4 text-sm text-pearl/60 backdrop-blur-liquid shadow-glass"
    >
        Mencari...
    </div>

    <div
        x-show="open && !loading"
        x-cloak
        class="absolute left-0 right-0 top-full z-50 mt-2 max-h-96 overflow-y-auto rounded-2xl bg-navy-soft/95 backdrop-blur-liquid shadow-glass"
    >
        <template x-if="query.trim().length >= 2 && empty()">
            <div class="p-4 text-sm text-pearl/50">Tidak ada hasil untuk "<span x-text="query"></span>".</div>
        </template>

        <template x-if="results.books.length">
            <div class="p-2">
                <p class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-azure-soft"><x-icon name="library" class="mr-1 inline h-3.5 w-3.5" /> Buku</p>
                <template x-for="(item, idx) in results.books" :key="'b' + idx">
                    <a :href="item.url" class="flex items-center gap-3 rounded-xl px-3 py-2 hover:bg-white/5" @click="open = false">
                        <span class="text-azure-soft"><x-icon name="book-open" class="h-4 w-4" /></span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-medium text-pearl" x-text="item.label"></span>
                            <span class="block text-xs text-pearl/40" x-text="item.sub"></span>
                        </span>
                    </a>
                </template>
            </div>
        </template>

        <template x-if="results.items.length">
            <div class="p-2">
                <p class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-azure-soft"><x-icon name="tag" class="mr-1 inline h-3.5 w-3.5" /> Eksemplar</p>
                <template x-for="(item, idx) in results.items" :key="'i' + idx">
                    <a :href="item.url" class="flex items-center gap-3 rounded-xl px-3 py-2 hover:bg-white/5" @click="open = false">
                        <span class="text-azure-soft"><x-icon name="tag" class="h-4 w-4" /></span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-medium text-pearl" x-text="item.label"></span>
                            <span class="block text-xs text-pearl/40" x-text="item.sub"></span>
                        </span>
                    </a>
                </template>
            </div>
        </template>

        <template x-if="results.members.length">
            <div class="p-2">
                <p class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-amber-warm"><x-icon name="users" class="mr-1 inline h-3.5 w-3.5" /> Anggota</p>
                <template x-for="(item, idx) in results.members" :key="'m' + idx">
                    <a :href="item.url" class="flex items-center gap-3 rounded-xl px-3 py-2 hover:bg-white/5" @click="open = false">
                        <span class="text-amber-warm"><x-icon name="user-round" class="h-4 w-4" /></span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-medium text-pearl" x-text="item.label"></span>
                            <span class="block text-xs text-pearl/40" x-text="item.sub"></span>
                        </span>
                    </a>
                </template>
            </div>
        </template>
    </div>
</div>

<script>
    function globalSearch() {
        return {
            query: '',
            results: { books: [], items: [], members: [] },
            open: false,
            loading: false,
            debounceTimer: null,
            activeIndex: -1,
            flat: [],

            init() {
                this.$watch('query', () => { this.activeIndex = -1; });
            },

            empty() {
                return this.results.books.length === 0
                    && this.results.items.length === 0
                    && this.results.members.length === 0;
            },

            async search() {
                const q = this.query.trim();
                if (q.length < 2) {
                    this.results = { books: [], items: [], members: [] };
                    this.open = false;
                    return;
                }

                this.loading = true;
                this.open = true;

                try {
                    const res = await fetch(`{{ route('api.search') }}?q=${encodeURIComponent(q)}`);
                    const data = await res.json();
                    this.results = data;
                    this.flat = [
                        ...data.books.map((r) => ({ ...r, group: 'books' })),
                        ...data.items.map((r) => ({ ...r, group: 'items' })),
                        ...data.members.map((r) => ({ ...r, group: 'members' })),
                    ];
                } catch (e) {
                    this.results = { books: [], items: [], members: [] };
                } finally {
                    this.loading = false;
                }
            },

            move(dir) {
                if (this.flat.length === 0) return;
                this.activeIndex = (this.activeIndex + dir + this.flat.length) % this.flat.length;
            },

            go() {
                if (this.activeIndex >= 0 && this.flat[this.activeIndex]) {
                    window.location.href = this.flat[this.activeIndex].url;
                }
            },
        };
    }
</script>