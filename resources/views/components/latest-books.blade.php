<!-- Daftar buku -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Buku</h6>
        <span class="text-muted small">{{ $books->count() }} buku</span>
    </div>
    <div class="card-body">
        @if ($books->isEmpty())
            <p class="text-muted mb-0">Belum ada buku pada daftar ini.</p>
        @else
            <div class="row">
                @foreach ($books as $book)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">
                            @if ($book->cover_url)
                                <img src="{{ asset('storage/' . $book->cover_url) }}"
                                     class="card-img-top"
                                     alt="Cover {{ $book->title }}"
                                     style="height: 180px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted"
                                     style="height: 180px;">
                                    <i class="fas fa-book fa-3x"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <h6 class="card-title mb-1 text-truncate" title="{{ $book->title }}">
                                    {{ $book->title }}
                                </h6>
                                <p class="card-text text-muted small mb-0">
                                    <i class="fas fa-tags mr-1"></i>{{ $book->category?->name ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
