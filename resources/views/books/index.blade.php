@extends('layouts.admin')
@section('title', 'Daftar Buku')
@section('content')
    <div class="container-fluid">

        <h1>Daftar Buku</h1>
        <a href="{{ route('books.create') }}" class="btn btn-primary mb-2">Tambah Buku</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books as $book)
                    <tr>
                        <td style="width: 70px;">
                            @if ($book->cover_url)
                                <img src="{{ asset('storage/' . $book->cover_url) }}"
                                     alt="Cover {{ $book->title }}"
                                     class="img-thumbnail"
                                     style="width: 50px; height: 70px; object-fit: cover;">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->user?->name }}</td>
                        <td>{{ $book->category?->name }}</td>
                        <td>
                            @can('update', $book)
                                <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Yakin hapus?')"
                                        class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            @else
                                <a href="{{ route('books.show', $book) }}" class="btn btn-info btn-sm">Lihat</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data buku</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $books->links('pagination::bootstrap-4') }}
    </div>
@endsection
