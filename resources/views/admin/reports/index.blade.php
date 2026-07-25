@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan Barang Rusak & Hilang')

@section('actions')
    <a href="{{ route('admin.export.reports.excel', request()->query()) }}" class="btn-success px-3 py-2 text-sm">Excel</a>
    <a href="{{ route('admin.export.reports.pdf', request()->query()) }}" class="btn-danger px-3 py-2 text-sm">PDF</a>
@endsection

@section('content')
    <div class="card mb-4 p-4">
        <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-4" data-no-loader>
            <select name="type" class="form-select">
                <option value="">Semua Jenis</option>
                @foreach($types as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="lab_id" class="form-select">
                <option value="">Semua Lab</option>
                @foreach($labs as $l)
                    <option value="{{ $l->id }}" @selected(request('lab_id') == $l->id)>{{ $l->nama }}</option>
                @endforeach
            </select>
            <button class="btn-secondary">Filter</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-default">
                <thead><tr><th>Tanggal</th><th>Jenis</th><th>Barang</th><th>Lokasi</th><th>Jumlah</th><th>Status</th><th>Pelapor</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($reports as $r)
                        <tr>
                            <td class="text-sm">{{ $r->reported_at?->format('d-m-Y H:i') }}</td>
                            <td><x-badge :class="$r->type->badge()">{{ $r->type->label() }}</x-badge></td>
                            <td class="font-medium">{{ $r->item?->nama }}</td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ collect([$r->lab?->nama, $r->group?->display_name, $r->labTable?->display_name])->filter()->implode(' • ') }}
                            </td>
                            <td>{{ $r->jumlah }}</td>
                            <td><x-badge :class="$r->status->badge()">{{ $r->status->label() }}</x-badge></td>
                            <td class="text-sm">{{ $r->user?->name }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.reports.show', $r) }}" class="btn-secondary px-3 py-1.5 text-xs">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty title="Belum ada laporan" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4"><x-paginator :paginator="$reports" /></div>
@endsection
