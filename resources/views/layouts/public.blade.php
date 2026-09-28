<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'Katalog Buku')</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Navigation -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow mb-4">

        <a class="navbar-brand font-weight-bold text-primary" href="{{ route('katalog.index') }}">
            <i class="fas fa-book-open mr-1"></i> Katalog Buku
        </a>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('katalog.index') ? 'active' : '' }}"
                   href="{{ route('katalog.index') }}">Daftar Buku</a>
            </li>
            <li class="topbar-divider d-none d-sm-block"></li>
            @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link">Logout</button>
                    </form>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}"
                       href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}"
                       href="{{ route('register') }}">Daftar</a>
                </li>
            @endauth
        </ul>

    </nav>
    <!-- End of Navigation -->

    <div class="container-fluid px-4">

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @yield('content')

    </div>

    <!-- Footer -->
    <footer class="sticky-footer bg-white mt-4">
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span>Copyright &copy; Your Website 2021</span>
            </div>
        </div>
    </footer>
    <!-- End of Footer -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

</body>

</html>
