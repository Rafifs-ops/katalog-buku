@extends('layouts.public')

@section('title', 'Katalog Buku')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Buku</h1>
        <span class="text-muted small">{{ $books->total() }} buku</span>
    </div>

    <div class="row">
        @forelse ($books as $book)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    @if ($book->cover_url)
                        <img src="{{ asset('storage/' . $book->cover_url) }}" class="card-img-top"
                             alt="Cover {{ $book->title }}" style="height: 220px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted"
                             style="height: 220px;">
                            <i class="fas fa-book fa-4x"></i>
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $book->title }}</h5>
                        <p class="card-text text-muted small mb-2">
                            <i class="fas fa-user mr-1"></i>{{ $book->user?->name ?? '-' }}
                            &middot;
                            <i class="fas fa-tags mr-1"></i>{{ $book->category?->name ?? '-' }}
                        </p>
                        <p class="card-text flex-grow-1">{{ Str::limit($book->description, 120) }}</p>
                        <a href="{{ route('katalog.show', $book) }}" class="btn btn-primary btn-sm mt-2 align-self-start">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Belum ada buku yang dipublikasikan.</div>
            </div>
        @endforelse
    </div>

    {{ $books->links('pagination::bootstrap-4') }}
@endsection
