@php
    $barcode = \DNS1D::getBarcodePNG($member->identity_number ?: $member->member_code, 'C128', 2, 45);
@endphp

<div style="width: 85.6mm; height: 53.98mm; border: 1px solid #ddd; border-radius: 6px; overflow: hidden; background: linear-gradient(135deg, #162032 0%, #2A3B54 100%); color: #fff; display: flex; flex-direction: column; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
    <div style="padding: 4mm 5mm; flex: 1;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div style="font-size: 7px; letter-spacing: 1px; text-transform: uppercase; color: rgba(255,255,255,0.6);">Perpustakaan {{ setting('school_name', 'SMP') }}</div>
                <div style="font-size: 11px; font-weight: 700; color: #97DDE9;">KARTU ANGGOTA</div>
            </div>
            <div style="font-size: 10px;">&#128218;</div>
        </div>

        <table style="width: 100%; font-size: 8px; margin-top: 3px; color: #fff;">
            <tr>
                <td style="width: 30%; color: rgba(255,255,255,0.6);">Nama</td>
                <td style="font-weight: 600;">: {{ $member->name }}</td>
            </tr>
            <tr>
                <td style="color: rgba(255,255,255,0.6);">NIS/NIP</td>
                <td>: {{ $member->identity_number ?? '-' }}</td>
            </tr>
            <tr>
                <td style="color: rgba(255,255,255,0.6);">Kelas/Jabatan</td>
                <td>: {{ $member->department_class ?? '-' }}</td>
            </tr>
        </table>

        {{-- Container putih bersih di belakang barcode agar mudah di-scan fisik --}}
        <div style="text-align: center; margin-top: 3px;">
            <div style="background: #ffffff; display: inline-block; padding: 3px 10px; border-radius: 4px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                <img src="data:image/png;base64,{{ $barcode }}" alt="barcode" style="width: 54mm; height: auto; display: block;" />
                <div style="font-family: monospace; font-size: 7px; letter-spacing: 1px; margin-top: 1px; color: #0f172a; text-align: center;">
                    {{ $member->identity_number ?: $member->member_code }}
                </div>
            </div>
        </div>
    </div>
</div>