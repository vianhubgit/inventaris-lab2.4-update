@extends('layouts.app')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan #' . $report->id)

@section('actions')
    <a href="{{ url()->previous(route('sekretaris.reports.index')) }}" class="btn-secondary">Kembali</a>
@endsection

@section('content')
    <div class="card max-w-2xl p-6">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div><dt class="text-xs uppercase text-gray-400">Jenis</dt><dd><x-badge :class="$report->type->badge()">{{ $report->type->label() }}</x-badge></dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Status</dt><dd><x-badge :class="$report->status->badge()">{{ $report->status->label() }}</x-badge></dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Barang</dt><dd class="font-medium">{{ $report->item?->nama }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Jumlah</dt><dd class="font-medium">{{ $report->jumlah }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-xs uppercase text-gray-400">Lokasi</dt><dd>{{ collect([$report->lab?->nama, $report->group?->display_name, $report->labTable?->display_name])->filter()->implode(' • ') }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-xs uppercase text-gray-400">Keterangan</dt><dd>{{ $report->keterangan ?: '—' }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-400">Tanggal</dt><dd>{{ $report->reported_at?->translatedFormat('d F Y H:i') }}</dd></div>
        </dl>

        @if($report->foto_url)
            <div class="mt-4">
                <p class="mb-2 text-xs uppercase text-gray-400">Foto</p>
                <img src="{{ $report->foto_url }}" class="max-h-72 rounded-lg border border-gray-200 dark:border-gray-700" alt="Foto laporan">
            </div>
        @endif

        @can('update', $report)
            <div class="mt-6 flex gap-3">
                <a href="{{ route('sekretaris.reports.edit', $report) }}" class="btn-secondary">Edit Laporan</a>
            </div>
        @endcan
    </div>
@endsection
