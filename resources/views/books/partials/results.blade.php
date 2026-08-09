<div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($books as $book)
        <div class="group glass-panel overflow-hidden rounded-4xl transition-transform duration-200 hover:-translate-y-1">
            <div class="flex h-40 items-center justify-center bg-gradient-to-br from-white/10 to-white/5">
                <div class="flex h-32 w-24 items-center justify-center rounded-r-lg bg-azure-soft/15 text-azure-soft shadow-[0_8px_20px_rgba(0,0,0,0.4)] transition-transform duration-300 group-hover:[transform:perspective(1000px)_rotateY(-15deg)]">
                    <x-icon name="book-open" class="h-8 w-8" />
                </div>
            </div>
            <div class="p-5">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="font-bold leading-snug text-pearl">{{ $book->title }}</h3>
                </div>
                <p class="mt-1 text-sm text-pearl/50">{{ $book->author }}</p>
                <div class="mt-3 flex items-center gap-2">
                    <x-badge variant="azure" dot>{{ $book->book_code }}</x-badge>
                </div>
                <div class="mt-3 flex items-center justify-between border-t border-white/10 pt-3 text-sm">
                    <span class="text-pearl/60">{{ $book->category->name ?? '-' }}</span>
                    <span class="font-semibold text-azure-soft">{{ $book->available_stock }}/{{ $book->total_stock }} tersedia</span>
                </div>
                <div class="mt-4 flex gap-2">
                    <x-button href="{{ route('books.show', $book) }}" variant="secondary" class="flex-1 px-3 py-2 text-xs">Detail</x-button>
                    <x-button href="{{ route('book-items.index', $book) }}" variant="secondary" class="flex-1 px-3 py-2 text-xs">Eksemplar</x-button>
                    <x-button href="{{ route('books.edit', $book) }}" variant="secondary" class="flex-1 px-3 py-2 text-xs">Edit</x-button>
                </div>
            </div>
        </div>
    @empty
        <div class="glass-panel col-span-full rounded-4xl p-10 text-center">
            <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-3xl bg-white/10 text-pearl/40"><x-icon name="library" class="h-8 w-8" /></span>
            <p class="mt-3 text-pearl/60">Rak ini masih menunggu cerita baru. Yuk, tambahkan buku pertama!</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $books->links() }}
</div>