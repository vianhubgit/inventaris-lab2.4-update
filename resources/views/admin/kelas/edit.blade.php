@extends('layouts.app')

@section('title', 'Edit Kelas')
@section('page-title', 'Edit Kelas')

@section('content')
    <div class="card max-w-xl p-6">
        <form method="POST" action="{{ route('admin.kelas.update', $kelas) }}" class="space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="_prev" value="{{ url()->previous() }}">
            <div>
                <label class="form-label">Nama Kelas <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $kelas->nama) }}" class="form-input" required>
                @error('nama')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan', $kelas->keterangan) }}" class="form-input">
                @error('keterangan')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3">
                <button class="btn-primary">Simpan Perubahan</button>
                <a href="{{ url()->previous(route('admin.kelas.index')) }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
