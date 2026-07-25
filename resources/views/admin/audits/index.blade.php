@extends('layouts.app')

@section('title', 'Audit Inventaris')
@section('page-title', 'Audit Inventaris')

@section('actions')
    <a href="{{ route('admin.export.audits.excel', request()->query()) }}" class="btn-success px-3 py-2 text-sm">Excel</a>
    <a href="{{ route('admin.export.audits.pdf', request()->query()) }}" class="btn-danger px-3 py-2 text-sm">PDF</a>
    <a href="{{ route('admin.audits.create') }}" class="btn-primary">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Audit Baru
    </a>
@endsection

@section('content')
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-default">
                <thead><tr><th>Tanggal</th><th>Barang</th><th>Tercatat</th><th>Fisik</th><th>Selisih</th><th>Petugas</th><th>Keterangan</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($audits as $a)
                        <tr>
                            <td class="text-sm">{{ $a->tanggal?->format('d-m-Y') }}</td>
                            <td class="font-medium">{{ $a->item?->nama }}</td>
                            <td>{{ $a->jumlah_tercatat }}</td>
                            <td>{{ $a->jumlah_fisik }}</td>
                            <td><span class="font-semibold {{ $a->selisih == 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $a->selisih > 0 ? '+' : '' }}{{ $a->selisih }}</span></td>
                            <td class="text-sm">{{ $a->user?->name ?? '—' }}</td>
                            <td class="max-w-xs truncate text-sm text-gray-500">{{ $a->keterangan ?: '—' }}</td>
                            <td class="text-right">
                                <form method="POST" action="{{ route('admin.audits.destroy', $a) }}" data-confirm="Hapus data audit?">
                                    @csrf @method('DELETE')
                                    <button class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty title="Belum ada data audit" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4"><x-paginator :paginator="$audits" /></div>
@endsection
