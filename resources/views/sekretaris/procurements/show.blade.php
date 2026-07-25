@extends('layouts.app')

@section('title', 'Detail Pengajuan')
@section('page-title', 'Pengajuan #' . $procurement->id)

@section('actions')
    <a href="{{ url()->previous(route('sekretaris.procurements.index')) }}" class="btn-secondary">Kembali</a>
@endsection

@section('content')
    <div class="card max-w-2xl p-6">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div><dt class="text-xs uppercase text-gray-400">Barang</dt><dd class="font-medium">{{ $procurement->nama_barang }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Status</dt><dd><x-badge :class="$procurement->status->badge()">{{ $procurement->status->label() }}</x-badge></dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Kelas Peminjam</dt><dd class="font-medium">{{ $procurement->kelas ?? '—' }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Jumlah Diminta</dt><dd class="font-medium">{{ $procurement->jumlah }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Jumlah Disetujui</dt><dd class="font-medium">{{ $procurement->jumlah_disetujui ?? '—' }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Tanggal</dt><dd>{{ $procurement->requested_at?->translatedFormat('d F Y H:i') }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-xs uppercase text-gray-400">Alasan</dt><dd>{{ $procurement->alasan }}</dd></div>
            @if($procurement->catatan_admin)
                <div class="sm:col-span-2"><dt class="text-xs uppercase text-gray-400">Catatan Admin</dt><dd>{{ $procurement->catatan_admin }}</dd></div>
            @endif
        </dl>

        @can('update', $procurement)
            <div class="mt-6">
                <a href="{{ route('sekretaris.procurements.edit', $procurement) }}" class="btn-secondary">Edit Pengajuan</a>
            </div>
        @endcan
    </div>
@endsection
