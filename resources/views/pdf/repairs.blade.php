<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Riwayat Perbaikan Barang</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #111; margin: 0; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 8px; margin-bottom: 12px; }
        .header h1 { margin: 0; font-size: 18px; color: #1e3a8a; }
        .header p { margin: 2px 0 0; color: #555; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #eff6ff; color: #1e3a8a; font-size: 10px; text-transform: uppercase; }
        tr:nth-child(even) td { background: #f9fafb; }
        .text-right { text-align: right; }
        .footer { margin-top: 12px; font-size: 9px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Riwayat Perbaikan Barang</h1>
        <p>Dicetak pada: {{ $tanggal }} &mdash; Total perbaikan: {{ $repairs->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th><th>Tanggal</th><th>Barang</th><th>Deskripsi</th>
                <th class="text-right">Biaya (Rp)</th><th>Status</th><th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($repairs as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $r->tanggal?->format('d-m-Y') }}</td>
                    <td>{{ $r->item?->nama }}</td>
                    <td>{{ $r->deskripsi }}</td>
                    <td class="text-right">{{ $r->biaya ? number_format($r->biaya, 0, ',', '.') : '-' }}</td>
                    <td>{{ $r->status->label() }}</td>
                    <td>{{ $r->user?->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Sistem Inventaris Lab TKJ &bull; Lab A, Lab B, TEFA</div>
</body>
</html>
