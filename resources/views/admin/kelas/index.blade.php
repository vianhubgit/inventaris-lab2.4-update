@extends('layouts.app')

@section('title', 'Kelas')
@section('page-title', 'Master Kelas Peminjam')

@section('actions')
    <a href="{{ route('admin.kelas.create') }}" class="btn-primary">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Kelas
    </a>
@endsection

@section('content')
    <div class="card mb-4 p-4">
        <form method="GET" class="flex gap-3" data-no-loader>
            <input type="text" name="q" value="{{ request('q') }}" class="form-input" placeholder="Cari kelas...">
            <button class="btn-secondary">Cari</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="table-default">
            <thead><tr><th>Nama Kelas</th><th>Keterangan</th><th>Dipakai</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
                @forelse($kelas as $k)
                    <tr>
                        <td class="font-medium">{{ $k->nama }}</td>
                        <td class="text-gray-500 dark:text-gray-400">{{ $k->keterangan ?: '—' }}</td>
                        <td><x-badge class="bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">{{ $k->usage_count }}</x-badge></td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.kelas.edit', $k) }}" class="btn-secondary px-3 py-1.5 text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.kelas.destroy', $k) }}" data-confirm="Hapus kelas {{ $k->nama }}?">
                                    @csrf @method('DELETE')
                                    <button class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4"><x-empty title="Belum ada kelas" /></td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4"><x-paginator :paginator="$kelas" /></div>
@endsection
