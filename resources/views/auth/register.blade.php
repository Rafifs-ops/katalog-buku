@extends('layouts.public')

@section('title', 'Register')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card border-0 shadow-sm o-hidden mb-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h1 class="h4 text-gray-900 mb-2">Buat Akun Baru</h1>
                        <p class="text-muted">Daftar untuk masuk ke panel admin.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="{{ old('name') }}" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                    </form>

                    <hr>
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="small">Sudah punya akun? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
