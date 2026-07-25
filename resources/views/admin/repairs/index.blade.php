@extends('layouts.app')

@section('title', 'Riwayat Perbaikan')
@section('page-title', 'Riwayat Perbaikan Barang')

@section('actions')
    <a href="{{ route('admin.export.repairs.excel', request()->query()) }}" class="btn-success px-3 py-2 text-sm">Excel</a>
    <a href="{{ route('admin.export.repairs.pdf', request()->query()) }}" class="btn-danger px-3 py-2 text-sm">PDF</a>
    <a href="{{ route('admin.repairs.create') }}" class="btn-primary">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Catat Perbaikan
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
                <thead><tr><th>Tanggal</th><th>Barang</th><th>Deskripsi</th><th>Biaya</th><th>Status</th><th>Petugas</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($repairs as $r)
                        <tr>
                            <td class="text-sm">{{ $r->tanggal?->format('d-m-Y') }}</td>
                            <td class="font-medium">{{ $r->item?->nama }}</td>
                            <td class="max-w-xs truncate text-sm text-gray-500 dark:text-gray-400">{{ $r->deskripsi }}</td>
                            <td>{{ $r->biaya ? 'Rp ' . number_format($r->biaya, 0, ',', '.') : '—' }}</td>
                            <td><x-badge :class="$r->status->badge()">{{ $r->status->label() }}</x-badge></td>
                            <td class="text-sm">{{ $r->user?->name ?? '—' }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.repairs.edit', $r) }}" class="btn-secondary px-3 py-1.5 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.repairs.destroy', $r) }}" data-confirm="Hapus riwayat ini?">
                                        @csrf @method('DELETE')
                                        <button class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty title="Belum ada riwayat perbaikan" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4"><x-paginator :paginator="$repairs" /></div>
@endsection
