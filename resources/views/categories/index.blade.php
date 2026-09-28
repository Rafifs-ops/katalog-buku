@extends('layouts.admin')
@section('title', 'Daftar Kategori')
@section('content')
    <div class="container-fluid">

        <h1>Daftar Kategori</h1>
        <a href="{{ route('categories.create') }}" class="btn btn-primary mb-2">Tambah Kategori</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jumlah Buku</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->books_count }}</td>
                        <td>
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Yakin hapus?')"
                                    class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data kategori</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $categories->links('pagination::bootstrap-4') }}
    </div>
@endsection
