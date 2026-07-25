@extends('layouts.app')

@section('title', 'Inventaris')
@section('page-title', 'Daftar Inventaris')
@section('breadcrumb', 'Hanya dapat dilihat (read-only)')

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
                            <td class="font-semibold">{{ $item->jumlah_total }}</td>
                            <td><x-badge :class="$item->status->badge()">{{ $item->status->label() }}</x-badge></td>
                            <td class="text-right">
                                <a href="{{ route('sekretaris.inventory.show', $item) }}" class="btn-secondary px-3 py-1.5 text-xs">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty title="Tidak ada barang" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4"><x-paginator :paginator="$items" /></div>
@endsection
