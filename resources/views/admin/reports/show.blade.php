@extends('layouts.app')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan #' . $report->id)

@section('actions')
    <a href="{{ url()->previous(route('admin.reports.index')) }}" class="btn-secondary">Kembali</a>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card p-6">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div><dt class="text-xs uppercase text-gray-400">Jenis</dt><dd><x-badge :class="$report->type->badge()">{{ $report->type->label() }}</x-badge></dd></div>
                    <div><dt class="text-xs uppercase text-gray-400">Status</dt><dd><x-badge :class="$report->status->badge()">{{ $report->status->label() }}</x-badge></dd></div>
                    <div><dt class="text-xs uppercase text-gray-400">Barang</dt><dd class="font-medium">{{ $report->item?->nama }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-400">Jumlah</dt><dd class="font-medium">{{ $report->jumlah }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs uppercase text-gray-400">Lokasi</dt><dd>{{ collect([$report->lab?->nama, $report->group?->display_name, $report->labTable?->display_name])->filter()->implode(' • ') }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-400">Pelapor</dt><dd>{{ $report->user?->name }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-400">Tanggal</dt><dd>{{ $report->reported_at?->translatedFormat('d F Y H:i') }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs uppercase text-gray-400">Keterangan</dt><dd>{{ $report->keterangan ?: '—' }}</dd></div>
                </dl>

                @if($report->foto_url)
                    <div class="mt-4">
                        <p class="mb-2 text-xs uppercase text-gray-400">Foto</p>
                        <a href="{{ $report->foto_url }}" target="_blank">
                            <img src="{{ $report->foto_url }}" alt="Foto laporan" class="max-h-72 rounded-lg border border-gray-200 dark:border-gray-700">
                        </a>
                    </div>
                @endif
            </div>

            @if($report->repairs->isNotEmpty())
                <div class="card p-6">
                    <h3 class="mb-4 font-semibold">Riwayat Perbaikan Terkait</h3>
                    @foreach($report->repairs as $r)
                        <div class="mb-3 flex items-start justify-between gap-3 border-b border-gray-100 pb-3 last:border-0 dark:border-gray-700">
                            <div><p class="text-sm">{{ $r->deskripsi }}</p><p class="text-xs text-gray-500">{{ $r->tanggal?->format('d-m-Y') }}</p></div>
                            <x-badge :class="$r->status->badge()">{{ $r->status->label() }}</x-badge>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Ubah status --}}
            <div class="card p-6">
                <h3 class="mb-4 font-semibold">Ubah Status</h3>
                <form method="POST" action="{{ route('admin.reports.status', $report) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($report->status->value === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="btn-primary w-full">Simpan Status</button>
                </form>
            </div>

            @if($report->type->value === 'rusak')
                <div class="card p-6">
                    <h3 class="mb-2 font-semibold">Tindak Lanjut</h3>
                    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Catat perbaikan untuk barang yang dilaporkan rusak.</p>
                    <a href="{{ route('admin.repairs.create', ['report_id' => $report->id]) }}" class="btn-secondary w-full justify-center">Buat Riwayat Perbaikan</a>
                </div>
            @endif

            <div class="card p-6">
                <form method="POST" action="{{ route('admin.reports.destroy', $report) }}" data-confirm="Hapus laporan ini?">
                    @csrf @method('DELETE')
                    <button class="btn-danger w-full">Hapus Laporan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
