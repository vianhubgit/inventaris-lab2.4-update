@extends('layouts.app')

@section('title', 'Barang')
@section('page-title', 'Inventaris Barang')

@section('actions')
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.export.items.excel', request()->query()) }}" class="btn-success px-3 py-2 text-sm">Excel</a>
        <a href="{{ route('admin.export.items.pdf', request()->query()) }}" class="btn-danger px-3 py-2 text-sm">PDF</a>
        <a href="{{ route('admin.items.create') }}" class="btn-primary">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Barang
        </a>
    </div>
@endsection

@section('content')
    <div class="card mb-4 p-4">
        <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5" data-no-loader>
            <input type="text" name="q" value="{{ request('q') }}" class="form-input lg:col-span-2" placeholder="Cari nama barang...">
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->nama }}</option>
                @endforeach
            </select>
            <select name="lab_id" class="form-select">
                <option value="">Semua Lab</option>
                @foreach($labs as $l)
                    <option value="{{ $l->id }}" @selected(request('lab_id') == $l->id)>{{ $l->nama }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn-secondary shrink-0">Filter</button>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-default">
                <thead><tr><th>Nama</th><th>Kategori</th><th>Lokasi</th><th>Jumlah</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="font-medium">{{ $item->nama }}</td>
                            <td>{{ $item->category?->nama }}</td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">{{ $item->lokasi_lengkap }}</td>
                            <td><span class="font-semibold">{{ $item->jumlah_total }}</span></td>
                            <td><x-badge :class="$item->status->badge()">{{ $item->status->label() }}</x-badge></td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.items.show', $item) }}" class="btn-secondary px-3 py-1.5 text-xs">Detail</a>
                                    <a href="{{ route('admin.items.edit', $item) }}" class="btn-secondary px-3 py-1.5 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.items.destroy', $item) }}" data-confirm="Hapus barang {{ $item->nama }}?">
                                        @csrf @method('DELETE')
                                        <button class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty title="Belum ada barang" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4"><x-paginator :paginator="$items" /></div>
@endsection
