@extends('layouts.admin')
@section('title', 'Detail Buku')
@section('content')
    <div class="container-fluid">

        <h1>Detail Buku</h1>
        <a href="{{ route('books.index') }}" class="btn btn-secondary mb-2">Kembali</a>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        @if ($book->cover_url)
                            <img src="{{ asset('storage/' . $book->cover_url) }}"
                                 alt="Cover {{ $book->title }}"
                                 class="img-thumbnail"
                                 style="width: 100%; max-width: 220px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted"
                                 style="height: 220px;">
                                <i class="fas fa-book fa-3x"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <h4 class="text-gray-800">{{ $book->title }}</h4>
                        <p class="text-muted mb-1">
                            <i class="fas fa-user mr-1"></i>Penulis: <strong>{{ $book->user?->name ?? '-' }}</strong>
                        </p>
                        <p class="text-muted mb-1">
                            <i class="fas fa-tags mr-1"></i>Kategori: <strong>{{ $book->category?->name ?? '-' }}</strong>
                        </p>
                        <p class="text-muted mb-1">
                            <i class="fas fa-calendar mr-1"></i>Dibuat: <strong>{{ $book->created_at?->format('d M Y') }}</strong>
                        </p>
                    </div>
                </div>

                <hr>

                <h6 class="text-gray-800">Deskripsi</h6>
                <p>{{ $book->description }}</p>

                <h6 class="text-gray-800">Konten</h6>
                <p>{!! nl2br(e($book->content)) !!}</p>
            </div>
        </div>
    </div>
@endsection
