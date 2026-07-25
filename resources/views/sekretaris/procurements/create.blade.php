@extends('layouts.app')

@section('title', 'Pinjam Barang')
@section('page-title', 'Peminjaman Barang')

@section('content')
    <div class="card max-w-2xl p-6">
        <form method="POST" action="{{ route('sekretaris.procurements.store') }}">
            @csrf
            @include('sekretaris.procurements._form')
        </form>
    </div>
@endsection
