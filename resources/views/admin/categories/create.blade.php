@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')
    <div class="card max-w-xl p-6">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" class="form-input" required>
                @error('nama')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Deskripsi</label>
                <input type="text" name="deskripsi" value="{{ old('deskripsi') }}" class="form-input">
                @error('deskripsi')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3">
                <button class="btn-primary">Simpan</button>
                <a href="{{ url()->previous(route('admin.categories.index')) }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
