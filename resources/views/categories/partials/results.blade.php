<div class="glass-panel overflow-hidden rounded-4xl">
    <x-table :headers="['Nama', 'Slug', 'Awalan', 'Jumlah Buku', 'Aksi']">
        @forelse ($categories as $category)
            <tr class="transition-colors hover:bg-white/5">
                <td class="px-4 py-3 font-medium text-pearl">{{ $category->name }}</td>
                <td class="px-4 py-3 text-pearl/60">{{ $category->slug }}</td>
                <td class="px-4 py-3">
                    <x-badge variant="azure">{{ $category->prefix }}</x-badge>
                </td>
                <td class="px-4 py-3 text-pearl/60">{{ $category->books_count }} buku</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('categories.edit', $category) }}" class="rounded-xl p-2 text-pearl/60 transition hover:bg-white/10 hover:text-azure-soft" title="Edit">
                            <span class="block h-4 w-4"><x-icon name="pencil" class="h-4 w-4" /></span>
                        </a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirmDelete(event, 'kategori')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl p-2 text-pearl/60 transition hover:bg-danger-soft hover:text-danger-red" title="Hapus">
                                <span class="block h-4 w-4"><x-icon name="trash-2" class="h-4 w-4" /></span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-10 text-center text-pearl/40">
                    Rak ini masih menunggu cerita baru. Yuk, tambahkan kategori pertama!
                </td>
            </tr>
        @endforelse
    </x-table>
    <div class="px-4 py-3">
        {{ $categories->links() }}
    </div>
</div>