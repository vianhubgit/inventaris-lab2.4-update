@extends('layouts.app')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('content')
    <div class="card max-w-xl p-6">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="_prev" value="{{ url()->previous() }}">
            <div>
                <label class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $category->nama) }}" class="form-input" required>
                @error('nama')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Deskripsi</label>
                <input type="text" name="deskripsi" value="{{ old('deskripsi', $category->deskripsi) }}" class="form-input">
                @error('deskripsi')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3">
                <button class="btn-primary">Simpan Perubahan</button>
                <a href="{{ url()->previous(route('admin.categories.index')) }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
