@props(['headers' => [], 'empty' => 'Belum ada data.', 'responsive' => true])

<div class="overflow-x-auto border-3 border-black bg-white shadow-brutal">
    <table {{ $attributes->merge(['class' => 'w-full min-w-max text-left text-xs']) }}>
        @if (count($headers) > 0)
            <thead>
                <tr class="border-b-3 border-black bg-brutal-yellow text-black">
                    @foreach ($headers as $header)
                        <th class="px-4 py-3 font-mono font-black uppercase tracking-widest border-r-2 border-black last:border-r-0">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y-2 divide-black font-semibold text-black">
            {{ $slot }}
        </tbody>
    </table>
    @if (trim($slot) == '')
        <div class="py-10 text-center font-mono text-xs font-bold uppercase tracking-widest text-black/60 bg-brutal-input">
            [!] {{ $empty }}
        </div>
    @endif
</div>

