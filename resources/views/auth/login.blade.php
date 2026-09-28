@extends('layouts.public')

@section('title', 'Login')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card border-0 shadow-sm o-hidden mb-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h1 class="h4 text-gray-900 mb-2">Selamat Datang Kembali!</h1>
                        <p class="text-muted">Silakan login untuk masuk ke panel admin.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group d-none">
                            <div class="custom-control custom-checkbox small">
                                <input type="checkbox" name="remember" id="remember" class="custom-control-input">
                                <label class="custom-control-label" for="remember">Ingat saya</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>

                    <hr>
                    <div class="text-center">
                        <a href="{{ route('register') }}" class="small">Belum punya akun? Daftar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
