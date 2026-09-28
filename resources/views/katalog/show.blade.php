@extends('layouts.public')

@section('title', $book->title)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-white shadow-sm">
            <li class="breadcrumb-item"><a href="{{ route('katalog.index') }}">Daftar Buku</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $book->title }}</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    @if ($book->cover_url)
                        <img src="{{ asset('storage/' . $book->cover_url) }}" alt="Cover {{ $book->title }}"
                             class="img-fluid rounded" style="width: 100%; max-height: 360px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                             style="height: 320px;">
                            <i class="fas fa-book fa-5x"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <h1 class="h3 text-gray-800">{{ $book->title }}</h1>

                    <p class="text-muted mb-2">
                        <i class="fas fa-user mr-1"></i>Penulis: <strong>{{ $book->user?->name ?? '-' }}</strong>
                        &middot;
                        <i class="fas fa-tags mr-1"></i>Kategori: <strong>{{ $book->category?->name ?? '-' }}</strong>
                    </p>

                    <p class="font-italic">{{ $book->description }}</p>

                    <hr>

                    <div class="book-content">
                        {!! nl2br(e($book->content)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('katalog.index') }}" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Buku
    </a>
@endsection
