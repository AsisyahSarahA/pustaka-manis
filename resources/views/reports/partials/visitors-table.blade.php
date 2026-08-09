<div class="glass-panel overflow-hidden rounded-4xl">
    <div class="flex items-center justify-between p-5">
        <h3 class="font-bold text-pearl">Daftar Kunjungan ({{ $visitors->count() }})</h3>
        <span class="text-xs text-pearl/40">Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} — {{ \Carbon\Carbon::parse($end)->format('d M Y') }}</span>
    </div>
    <x-table :headers="['Tanggal', 'Jam', 'Tipe', 'Nama', 'Instansi / Kelas', 'Tujuan']">
        @forelse ($visitors as $visitor)
            <tr class="transition-colors hover:bg-white/5">
                <td class="px-4 py-3 text-pearl/60">{{ optional($visitor->visit_date)->format('d M Y') }}</td>
                <td class="px-4 py-3 font-mono text-sm text-pearl/60">{{ $visitor->check_in_time }}</td>
                <td class="px-4 py-3">
                    <x-badge :variant="$visitor->visitor_type === 'siswa' ? 'blue' : ($visitor->visitor_type === 'guru' ? 'azure' : 'amber')">{{ $visitor->visitor_type_label }}</x-badge>
                </td>
                <td class="px-4 py-3 font-medium text-pearl">{{ $visitor->member?->name ?? $visitor->guest_name }}</td>
                <td class="px-4 py-3 text-pearl/60">{{ $visitor->member?->department_class ?? $visitor->guest_origin ?? '-' }}</td>
                <td class="px-4 py-3 text-pearl/60">{{ $visitor->purpose }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-10 text-center text-pearl/40">Tidak ada kunjungan pada periode ini.</td>
            </tr>
        @endforelse
    </x-table>
</div>