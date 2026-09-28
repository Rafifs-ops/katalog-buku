@extends('layouts.admin')
@section('title', 'Edit Kategori')
@section('content')
    <div class="container-fluid">

        <h1>Edit Kategori</h1>
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label>Nama Kategori</label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $category->name) }}">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
