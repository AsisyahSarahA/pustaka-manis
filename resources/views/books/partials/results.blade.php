<div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($books as $book)
        <div class="group glass-panel overflow-hidden transition-transform duration-75 hover:-translate-y-1 hover:shadow-brutal-lg flex flex-col justify-between">
            <div>
                {{-- Cover Header --}}
                <div class="flex h-48 items-center justify-center border-b-3 border-black bg-brutal-input p-4 overflow-hidden relative">
                    @if ($book->cover_url)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="h-40 w-28 border-2 border-black object-cover shadow-brutal-sm transition-transform duration-150 group-hover:scale-105" />
                    @else
                        <div class="flex h-36 w-24 items-center justify-center border-2 border-black bg-white shadow-brutal-sm">
                            <span class="text-3xl">📖</span>
                        </div>
                    @endif

                    @if ($book->available_stock === 0)
                        <div class="stamp-checked-out">HABIS</div>
                    @endif
                </div>

                {{-- Book Details --}}
                <div class="p-5">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-black leading-snug text-black uppercase font-heading line-clamp-2">{{ $book->title }}</h3>
                    </div>
                    <p class="mt-1 text-xs font-bold text-black/70">{{ $book->author }}</p>

                    <div class="mt-3 flex items-center gap-2">
                        <x-badge variant="azure" dot>{{ $book->book_code }}</x-badge>
                        @if($book->rack_location)
                            <span class="border border-black bg-brutal-yellow px-1.5 py-0.5 font-mono text-[10px] font-black text-black">RAK {{ $book->rack_location }}</span>
                        @endif
                    </div>

                    <div class="mt-3 flex items-center justify-between border-t-2 border-black pt-3 text-xs font-bold">
                        <span class="text-black/70">{{ $book->category->name ?? '-' }}</span>
                        <span class="font-mono {{ $book->available_stock > 0 ? 'text-black' : 'text-brutal-pink' }}">
                            {{ $book->available_stock }}/{{ $book->total_stock }} TERSEDIA
                        </span>
                    </div>
                </div>
            </div>

            {{-- Card Actions --}}
            <div class="p-5 pt-0">
                <div class="grid grid-cols-3 gap-2">
                    <x-button href="{{ route('books.show', $book) }}" variant="secondary" class="px-2 py-2 text-[11px] font-black shadow-brutal-sm">DETAIL</x-button>
                    <x-button href="{{ route('book-items.index', $book) }}" variant="secondary" class="px-2 py-2 text-[11px] font-black shadow-brutal-sm">EKSEMPLAR</x-button>
                    <x-button href="{{ route('books.edit', $book) }}" variant="yellow" class="px-2 py-2 text-[11px] font-black shadow-brutal-sm">EDIT</x-button>
                </div>
            </div>
        </div>
    @empty
        <div class="glass-panel col-span-full p-10 text-center">
            <span class="mx-auto inline-flex h-16 w-16 items-center justify-center border-3 border-black bg-brutal-yellow text-black shadow-brutal"><x-icon name="library" class="h-8 w-8" /></span>
            <p class="mt-4 text-sm font-black uppercase text-black font-heading">Koleksi Masih Kosong</p>
            <p class="text-xs font-medium text-black/60 mt-1">Rak ini masih menunggu cerita baru. Yuk, tambahkan buku pertama!</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $books->links() }}
</div>