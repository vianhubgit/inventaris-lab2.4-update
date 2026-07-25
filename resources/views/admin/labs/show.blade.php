@extends('layouts.app')

@section('title', 'Tata Letak ' . $lab->nama)
@section('page-title', 'Tata Letak — ' . $lab->nama)
@section('breadcrumb', 'Kelola ' . ($lab->kode == 'TEFA' ? 'lemari dan rak' : 'kelompok dan meja') . ' di laboratorium ini')

@section('actions')
    <a href="{{ url()->previous(route('admin.labs.index')) }}" class="btn-secondary">Kembali</a>
@endsection

@section('content')

    {{-- TAMBAH GROUP (KELOMPOK / LEMARI) --}}
    <div class="card mb-6 p-5">
        <h3 class="mb-3 font-semibold">
            Tambah {{ $lab->kode == 'TEFA' ? 'Lemari' : 'Kelompok' }}
        </h3>

        <form method="POST" action="{{ route('admin.labs.groups.store', $lab) }}" class="flex flex-wrap items-end gap-3">
            @csrf

            <div class="w-32">
                <label class="form-label">
                    {{ $lab->kode == 'TEFA' ? 'Nomor Lemari' : 'Nomor' }}
                </label>

                <input type="number"
                       name="nomor"
                       min="1"
                       class="form-input"
                       value="{{ old('nomor', ($lab->groups->max('nomor') ?? 0) + 1) }}"
                       required>
            </div>

            <div class="grow">
                <label class="form-label">Nama (opsional)</label>

                <input type="text"
                       name="nama"
                       class="form-input"
                       placeholder="{{ $lab->kode == 'TEFA' ? 'mis.Lemari A' : 'mis.Kelompok 1' }}">
            </div>

            <button class="btn-primary">
                {{ $lab->kode == 'TEFA' ? 'Tambah Lemari' : 'Tambah Kelompok' }}
            </button>
        </form>
    </div>

    {{-- LIST GROUP --}}
    @forelse($lab->groups as $group)

        <div class="card mb-4 p-5">

            {{-- HEADER GROUP --}}
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
<h3 class="text-lg font-bold">
@if($lab->kode == 'TEFA')
    {{ $group->nama }}
@else
    {{ $group->display_name }}
@endif                </h3>

                <div class="flex items-center gap-2">

                    {{-- EDIT GROUP --}}
                    <form method="POST"
                          action="{{ route('admin.labs.groups.update', [$lab, $group]) }}"
                          class="flex items-center gap-2">

                        @csrf
                        @method('PUT')

                        <input type="number"
                               name="nomor"
                               min="1"
                               value="{{ $group->nomor }}"
                               class="form-input w-20 py-1.5 text-sm"
                               required>

                        <input type="text"
                               name="nama"
                               value="{{ $group->nama }}"
                               class="form-input w-40 py-1.5 text-sm"
                               placeholder="Nama">

                        <button class="btn-secondary px-3 py-1.5 text-xs">
                            Simpan
                        </button>
                    </form>

                    {{-- DELETE GROUP --}}
                    <form method="POST"
                          action="{{ route('admin.labs.groups.destroy', [$lab, $group]) }}"
                          data-confirm="Hapus {{ $group->display_name }} beserta semua item?">

                        @csrf
                        @method('DELETE')

                        <button class="btn-danger px-3 py-1.5 text-xs">
                            {{ $lab->kode == 'TEFA' ? 'Hapus Lemari' : 'Hapus Kelompok' }}
                        </button>
                    </form>

                </div>
            </div>

            {{-- TABLES / RAK --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">

                @foreach($group->tables as $table)

                    <div class="rounded-lg border border-gray-200 p-3 text-center dark:border-gray-700">

                        <div class="mb-2 flex h-10 items-center justify-center rounded bg-brand-50 font-semibold text-brand-700 dark:bg-brand-900/30 dark:text-brand-300">

                            @if($lab->kode == 'TEFA')
                                {{ $table->nama ?? "Rak {$table->nomor}" }}
                            @else
                                {{ $table->display_name }}
                            @endif

                        </div>

                        <form method="POST"
                              action="{{ route('admin.groups.tables.destroy', [$group, $table]) }}"
                              data-confirm="Hapus {{ $table->display_name }}?">

                            @csrf
                            @method('DELETE')

                            <button class="text-xs text-red-500 hover:underline">
                                Hapus
                            </button>

                        </form>

                    </div>

                @endforeach

            </div>

            {{-- ADD TABLE (MEJA / RAK) --}}
            <form method="POST"
                  action="{{ route('admin.groups.tables.store', $group) }}"
                  class="mt-4 flex flex-wrap items-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-700">

                @csrf

                <div class="w-28">
                    <label class="form-label text-xs">
                        {{ $lab->kode == 'TEFA' ? 'Nomor Rak' : 'Nomor Meja' }}
                    </label>

                    <input type="number"
                           name="nomor"
                           min="1"
                           class="form-input py-1.5"
                           value="{{ ($group->tables->max('nomor') ?? 0) + 1 }}"
                           required>
                </div>

                <div class="w-40">
                    <label class="form-label text-xs">Nama (opsional)</label>

                    <input type="text"
                           name="nama"
                           class="form-input py-1.5"
                           placeholder="{{ $lab->kode == 'TEFA' ? 'mis.Rak 1' : 'mis.Meja 1' }}">
                </div>

                <button class="btn-secondary px-3 py-1.5 text-xs">
                    + {{ $lab->kode == 'TEFA' ? 'Tambah Rak' : 'Tambah Meja' }}
                </button>

            </form>

        </div>

    @empty

        <div class="card">
            <x-empty
                title="Belum ada {{ $lab->kode == 'TEFA' ? 'lemari' : 'kelompok' }}"
                subtitle="Tambahkan terlebih dahulu di atas."
            />
        </div>

    @endforelse

@endsection
