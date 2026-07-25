@extends('layouts.app')

@section('title', 'Riwayat Laporan')
@section('page-title', 'Riwayat Laporan Saya')

@section('actions')
    <a href="{{ route('sekretaris.reports.create', ['type' => 'rusak']) }}" class="btn-warning">+ Rusak</a>
    <a href="{{ route('sekretaris.reports.create', ['type' => 'hilang']) }}" class="btn-secondary">+ Hilang</a>
    <a href="{{ route('sekretaris.reports.create', ['type' => 'umum']) }}" class="btn-secondary">+ Barang TEFA</a>
@endsection

@section('content')
    <div class="card mb-4 p-4">
        <form method="GET" class="flex gap-3" data-no-loader>
            <select name="type" class="form-select max-w-xs">
                <option value="">Semua Jenis</option>
                @foreach($types as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn-secondary">Filter</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-default">
                <thead><tr><th>Tanggal</th><th>Jenis</th><th>Barang</th><th>Lokasi</th><th>Jumlah</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($reports as $r)
                        <tr>
                            <td class="text-sm">{{ $r->reported_at?->format('d-m-Y H:i') }}</td>
                            <td><x-badge :class="$r->type->badge()">{{ $r->type->label() }}</x-badge></td>
                            <td class="font-medium">{{ $r->item?->nama }}</td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">{{ collect([$r->lab?->nama, $r->group?->display_name, $r->labTable?->display_name])->filter()->implode(' • ') }}</td>
                            <td>{{ $r->jumlah }}</td>
                            <td><x-badge :class="$r->status->badge()">{{ $r->status->label() }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('sekretaris.reports.show', $r) }}" class="btn-secondary px-3 py-1.5 text-xs">Detail</a>
                                    @can('update', $r)
                                        <a href="{{ route('sekretaris.reports.edit', $r) }}" class="btn-secondary px-3 py-1.5 text-xs">Edit</a>
                                        <form method="POST" action="{{ route('sekretaris.reports.destroy', $r) }}" data-confirm="Hapus laporan ini?">
                                            @csrf @method('DELETE')
                                            <button class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty title="Belum ada laporan" subtitle="Buat laporan rusak atau hilang melalui menu di samping." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4"><x-paginator :paginator="$reports" /></div>
@endsection
