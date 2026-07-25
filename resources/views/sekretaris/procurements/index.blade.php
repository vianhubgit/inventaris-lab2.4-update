@extends('layouts.app')

@section('title', 'Status Pinjaman')
@section('page-title', 'Status Pinjaman Saya')

@section('actions')
    <a href="{{ route('sekretaris.procurements.create') }}" class="btn-primary">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajukan Peminjaman
    </a>
@endsection

@section('content')
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-default">
                <thead><tr><th>Tanggal</th><th>Barang</th><th>Kelas</th><th>Jumlah</th><th>Status</th><th>Catatan Admin</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($procurements as $p)
                        <tr>
                            <td class="text-sm">{{ $p->requested_at?->format('d-m-Y') }}</td>
                            <td class="font-medium">{{ $p->nama_barang }}</td>
                            <td class="text-sm">{{ $p->kelas ?? '—' }}</td>
                            <td>{{ $p->jumlah }}@if(!is_null($p->jumlah_disetujui)) <span class="text-xs text-emerald-600 dark:text-emerald-400">(disetujui {{ $p->jumlah_disetujui }})</span>@endif</td>
                            <td><x-badge :class="$p->status->badge()">{{ $p->status->label() }}</x-badge></td>
                            <td class="max-w-xs truncate text-sm text-gray-500">{{ $p->catatan_admin ?: '—' }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('sekretaris.procurements.show', $p) }}" class="btn-secondary px-3 py-1.5 text-xs">Detail</a>
                                    @can('update', $p)
                                        <a href="{{ route('sekretaris.procurements.edit', $p) }}" class="btn-secondary px-3 py-1.5 text-xs">Edit</a>
                                        <form method="POST" action="{{ route('sekretaris.procurements.destroy', $p) }}" data-confirm="Batalkan pinjaman ini?">
                                            @csrf @method('DELETE')
                                            <button class="btn-danger px-3 py-1.5 text-xs">Batal</button>
                                        </form>
                                    @endcan
                                </div>
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
