@extends('layouts.app')

@section('title', 'Tambah Kelas')
@section('page-title', 'Tambah Kelas')

@section('content')
    <div class="card max-w-xl p-6">
        <form method="POST" action="{{ route('admin.kelas.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Nama Kelas <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" class="form-input" placeholder="mis. X TKJ A" required>
                @error('nama')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan') }}" class="form-input">
                @error('keterangan')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3">
                <button class="btn-primary">Simpan</button>
                <a href="{{ url()->previous(route('admin.kelas.index')) }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
