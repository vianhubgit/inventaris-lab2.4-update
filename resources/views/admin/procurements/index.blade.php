@extends('layouts.app')

@section('title', 'Peminjaman')
@section('page-title', 'Pinjaman Barang')
@section('actions')

<a href="{{ route('admin.export.procurements.excel', request()->query()) }}"
   class="btn-success px-3 py-2 text-sm">
    Excel
</a>

<a href="{{ route('admin.export.procurements.pdf', request()->query()) }}"
   class="btn-danger px-3 py-2 text-sm">
    PDF
</a>

@endsection
@section('content')

    <div class="card mb-4 p-4">
        <form method="GET" class="flex gap-3" data-no-loader>
            <select name="status" class="form-select max-w-xs">
                <option value="">Semua Status</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn-secondary">Filter</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-default">
                <thead><tr><th>Tanggal</th><th>Barang</th><th>Kelas</th><th>Jumlah</th><th>Pengaju</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($procurements as $p)
                        <tr>
                            <td class="text-sm">{{ $p->requested_at?->format('d-m-Y') }}</td>
                            <td class="font-medium">{{ $p->nama_barang }}</td>
                            <td class="text-sm">{{ $p->kelas ?? '—' }}</td>
                            <td>{{ $p->jumlah }}@if(!is_null($p->jumlah_disetujui)) <span class="text-xs text-emerald-600 dark:text-emerald-400">(disetujui {{ $p->jumlah_disetujui }})</span>@endif</td>
                            <td class="text-sm">{{ $p->user?->name }}</td>
                            <td><x-badge :class="$p->status->badge()">{{ $p->status->label() }}</x-badge></td>
                            <td class="text-right">
                                <a href="{{ route('admin.procurements.show', $p) }}" class="btn-secondary px-3 py-1.5 text-xs">Proses</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty title="Belum ada pengajuan" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4"><x-paginator :paginator="$procurements" /></div>
@endsection
