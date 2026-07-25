@extends('layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Kategori Barang')

@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn-primary">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Kategori
    </a>
@endsection

@section('content')
    <div class="card mb-4 p-4">
        <form method="GET" class="flex gap-3" data-no-loader>
            <input type="text" name="q" value="{{ request('q') }}" class="form-input" placeholder="Cari kategori...">
            <button class="btn-secondary">Cari</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="table-default">
            <thead><tr><th>Nama Kategori</th><th>Deskripsi</th><th>Jumlah Barang</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
                @forelse($categories as $c)
                    <tr>
                        <td class="font-medium">{{ $c->nama }}</td>
                        <td class="text-gray-500 dark:text-gray-400">{{ $c->deskripsi ?: '—' }}</td>
                        <td><x-badge class="bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">{{ $c->items_count }}</x-badge></td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $c) }}" class="btn-secondary px-3 py-1.5 text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" data-confirm="Hapus kategori {{ $c->nama }}?">
                                    @csrf @method('DELETE')
                                    <button class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4"><x-empty title="Belum ada kategori" /></td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4"><x-paginator :paginator="$categories" /></div>
@endsection
