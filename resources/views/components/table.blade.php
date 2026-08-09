@props(['headers' => [], 'empty' => 'Belum ada data.', 'responsive' => true])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'w-full min-w-max text-left text-sm']) }}>
        @if (count($headers) > 0)
            <thead>
                <tr class="border-b border-white/10 text-xs uppercase tracking-wider text-pearl/40">
                    @foreach ($headers as $header)
                        <th class="px-4 py-3 font-semibold">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-white/5">
            {{ $slot }}
        </tbody>
    </table>
    @if (trim($slot) == '')
        <div class="py-10 text-center text-sm text-pearl/40">{{ $empty }}</div>
    @endif
</div>
