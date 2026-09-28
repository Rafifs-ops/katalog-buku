@extends('layouts.admin')
@section('title', 'Edit Buku')
@section('content')
    <div class="container-fluid">

        <h1>Edit Buku</h1>
        <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-3">
                <label>Judul</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $book->title) }}">
                @error('title')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="description" rows="3" class="form-control">{{ old('description', $book->description) }}</textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label>Konten</label>
                <textarea name="content" rows="5" class="form-control">{{ old('content', $book->content) }}</textarea>
                @error('content')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label>Kategori</label>
                <select name="category_id" class="form-control">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label>Cover</label>
                <input type="file" name="cover_url" id="cover_url" class="form-control"
                       accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewCover(event)">
                @error('cover_url')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
                <div class="mt-2">
                    @if ($book->cover_url)
                        <img id="cover-preview" src="{{ asset('storage/' . $book->cover_url) }}"
                             alt="Cover {{ $book->title }}"
                             class="img-thumbnail" style="max-width: 150px; max-height: 200px;">
                    @else
                        <img id="cover-preview" src="" alt="Preview cover"
                             class="img-thumbnail d-none" style="max-width: 150px; max-height: 200px;">
                    @endif
                    <div class="form-text">Kosongkan jika cover tidak diganti.</div>
                </div>
            </div>
            <button class="btn btn-success">Update</button>
        </form>
    </div>

    <script>
        function previewCover(event) {
            const input = event.target;
            const preview = document.getElementById('cover-preview');

            if (!input.files || !input.files[0]) {
                return;
            }

            preview.src = URL.createObjectURL(input.files[0]);
            preview.classList.remove('d-none');
        }
    </script>
@endsection
