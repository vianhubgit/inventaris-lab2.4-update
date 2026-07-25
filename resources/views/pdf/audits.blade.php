<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Audit Inventaris</title>
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
        .text-center { text-align: center; }
        .footer { margin-top: 12px; font-size: 9px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Audit Inventaris</h1>
        <p>Dicetak pada: {{ $tanggal }} &mdash; Total audit: {{ $audits->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th><th>Tanggal</th><th>Barang</th>
                <th class="text-center">Tercatat</th><th class="text-center">Fisik</th><th class="text-center">Selisih</th>
                <th>Petugas</th><th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($audits as $i => $a)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $a->tanggal?->format('d-m-Y') }}</td>
                    <td>{{ $a->item?->nama }}</td>
                    <td class="text-center">{{ $a->jumlah_tercatat }}</td>
                    <td class="text-center">{{ $a->jumlah_fisik }}</td>
                    <td class="text-center">{{ $a->selisih > 0 ? '+' : '' }}{{ $a->selisih }}</td>
                    <td>{{ $a->user?->name }}</td>
                    <td>{{ $a->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Sistem Inventaris Lab TKJ &bull; Lab A, Lab B, TEFA</div>
</body>
</html>
