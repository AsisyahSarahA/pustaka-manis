<div class="glass-panel overflow-hidden rounded-4xl">
    <x-table :headers="['Kode', 'Nama', 'Tipe', 'NIS/NIP', 'Kelas/Jabatan', 'Status', 'Aksi']">
        @forelse ($members as $member)
            <tr class="transition-colors hover:bg-white/5">
                <td class="px-4 py-3 font-mono text-sm text-azure-soft">{{ $member->member_code }}</td>
                <td class="px-4 py-3 font-medium text-pearl">{{ $member->name }}</td>
                <td class="px-4 py-3">
                    <x-badge :variant="$member->type === 'siswa' ? 'blue' : ($member->type === 'guru' ? 'azure' : ($member->type === 'staf' ? 'amber' : 'neutral'))">{{ $member->type_label }}</x-badge>
                </td>
                <td class="px-4 py-3 text-pearl/60">{{ $member->identity_number ?? '-' }}</td>
                <td class="px-4 py-3 text-pearl/60">{{ $member->department_class ?? '-' }}</td>
                <td class="px-4 py-3">
                    @if ($member->is_active)
                        <x-badge variant="success" dot>Aktif</x-badge>
                    @else
                        <x-badge variant="red" dot>Nonaktif</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-1">
                        <a href="{{ route('members.show', $member) }}" class="rounded-xl p-2 text-pearl/60 transition hover:bg-white/10 hover:text-azure-soft" title="Detail">
                            <span class="block h-4 w-4"><x-icon name="eye" class="h-4 w-4" /></span>
                        </a>
                        <a href="{{ route('members.edit', $member) }}" class="rounded-xl p-2 text-pearl/60 transition hover:bg-white/10 hover:text-azure-soft" title="Edit">
                            <span class="block h-4 w-4"><x-icon name="pencil" class="h-4 w-4" /></span>
                        </a>
                        <a href="{{ route('members.card', $member) }}" target="_blank" class="rounded-xl p-2 text-pearl/60 transition hover:bg-white/10 hover:text-amber-warm" title="Cetak Kartu">
                            <span class="block h-4 w-4"><x-icon name="printer" class="h-4 w-4" /></span>
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-10 text-center text-pearl/40">
                    Belum ada anggota terdaftar. Tambahkan anggota pertama!
                </td>
            </tr>
        @endforelse
    </x-table>
    <div class="px-4 py-3">
        {{ $members->links() }}
    </div>
</div>