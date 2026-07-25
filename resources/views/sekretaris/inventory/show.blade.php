@extends('layouts.app')

@section('title', $item->nama)
@section('page-title', $item->nama)

@section('actions')
    <a href="{{ url()->previous(route('sekretaris.inventory.index')) }}" class="btn-secondary">Kembali</a>
    <a href="{{ route('sekretaris.reports.create', ['type' => 'rusak']) }}" class="btn-warning">Laporkan</a>
@endsection

@section('content')
    <div class="card max-w-2xl p-6">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div><dt class="text-xs uppercase text-gray-400">Kategori</dt><dd class="font-medium">{{ $item->category?->nama }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Status</dt><dd><x-badge :class="$item->status->badge()">{{ $item->status->label() }}</x-badge></dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Lokasi</dt><dd class="font-medium">{{ $item->lokasi_lengkap }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Jumlah Total</dt><dd class="text-2xl font-bold">{{ $item->jumlah_total }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-xs uppercase text-gray-400">Keterangan</dt><dd>{{ $item->keterangan ?: '—' }}</dd></div>
        </dl>
    </div>
@endsection
