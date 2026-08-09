@extends('reports.pdf.layout')

@section('content')
    {{-- Ringkasan Eksekutif --}}
    <table class="summary-grid">
        <tr>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="val">{{ number_format($total_titles ?? count($books)) }}</div>
                    <div class="lbl">Judul Buku</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="val">{{ number_format($total_items ?? 0) }}</div>
                    <div class="lbl">Total Eksemplar</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="val" style="color: #16a34a;">{{ number_format($available_items ?? 0) }}</div>
                    <div class="lbl">Tersedia</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="val" style="color: #2563eb;">{{ number_format($borrowed_items ?? 0) }}</div>
                    <div class="lbl">Dipinjam</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="val" style="color: #dc2626;">{{ number_format(($damaged_items ?? 0) + ($lost_items ?? 0)) }}</div>
                    <div class="lbl">Rusak / Hilang</div>
                </div>
            </td>
        </tr>
    </table>

    <h3 style="font-size: 11px; margin: 10px 0 6px; color: #0f172a; font-weight: bold; text-transform: uppercase;">Daftar Koleksi Inventaris Buku</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Kode Buku</th>
                <th style="width: 32%;">Judul Buku</th>
                <th style="width: 18%;">Kategori</th>
                <th style="width: 15%;">Penulis / Penerbit</th>
                <th style="width: 7%; text-align: center;">Total</th>
                <th style="width: 8%; text-align: center;">Tersedia</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($books as $index => $book)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-weight: bold; font-family: monospace;">{{ $book->book_code }}</td>
                    <td style="font-weight: 600; color: #0f172a;">{{ $book->title }}</td>
                    <td>{{ $book->category->name ?? '-' }}</td>
                    <td>{{ $book->author ?? '-' }} ({{ $book->publisher ?? '-' }})</td>
                    <td style="text-align: center; font-weight: bold;">{{ $book->items_count ?? $book->items->count() }}</td>
                    <td style="text-align: center; color: #16a34a; font-weight: bold;">
                        {{ $book->items->where('status', 'tersedia')->count() }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b; padding: 15px;">Tidak ada data koleksi buku.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right; text-transform: uppercase;">Total Akumulasi Eksemplar:</td>
                <td style="text-align: center;">{{ $books->sum(fn($b) => $b->items_count ?? $b->items->count()) }}</td>
                <td style="text-align: center; color: #16a34a;">{{ $books->sum(fn($b) => $b->items->where('status', 'tersedia')->count()) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection