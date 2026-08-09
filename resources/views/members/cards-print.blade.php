@extends('layouts.print')

@section('title', 'Cetak Kartu Anggota')

@section('content')
    <style>
        .cards-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 5mm;
            justify-content: center;
            padding: 2mm;
        }
        .card-item {
            page-break-inside: avoid;
            break-inside: avoid;
            margin-bottom: 3mm;
        }
        @media print {
            .cards-wrap { gap: 5mm; }
        }
    </style>

    <div class="cards-wrap">
        @forelse ($members as $member)
            <div class="card-item">
                @include('members.partials.card-single', ['member' => $member])
            </div>
        @empty
            <div style="text-align:center; padding:40px 0; color:#64748b; font-family:Arial, sans-serif;">
                Tidak ada anggota yang bisa dicetak.
            </div>
        @endforelse
    </div>
@endsection
