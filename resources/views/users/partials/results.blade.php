<div class="glass-panel overflow-hidden rounded-4xl">
    <x-table :headers="['Nama', 'Username', 'Email', 'Role', 'Status', 'Aksi']">
        @forelse ($users as $user)
            <tr class="transition-colors hover:bg-white/5">
                <td class="px-4 py-3 font-medium text-pearl">{{ $user->name }}</td>
                <td class="px-4 py-3 font-mono text-sm text-azure-soft">{{ $user->username }}</td>
                <td class="px-4 py-3 text-pearl/60">{{ $user->email ?? '-' }}</td>
                <td class="px-4 py-3">
                    <x-badge :variant="$user->role === 'admin' ? 'amber' : ($user->role === 'pustakawan' ? 'azure' : 'neutral')">{{ ucfirst($user->role) }}</x-badge>
                </td>
                <td class="px-4 py-3">
                    @if ($user->is_active)
                        <x-badge variant="success" dot>Aktif</x-badge>
                    @else
                        <x-badge variant="red" dot>Nonaktif</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="rounded-xl p-2 text-pearl/60 transition hover:bg-white/10 hover:text-azure-soft" title="Edit">
                            <span class="block h-4 w-4"><x-icon name="pencil" class="h-4 w-4" /></span>
                        </a>
                        @if ($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirmDelete(event, 'pengguna')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl p-2 text-pearl/60 transition hover:bg-danger-soft hover:text-danger-red" title="Hapus">
                                    <span class="block h-4 w-4"><x-icon name="trash-2" class="h-4 w-4" /></span>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-10 text-center text-pearl/40">Belum ada pengguna terdaftar.</td>
            </tr>
        @endforelse
    </x-table>
    <div class="px-4 py-3">
        {{ $users->links() }}
    </div>
</div>