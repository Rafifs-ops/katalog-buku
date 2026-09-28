@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
    <x-topbar />

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        @if ($is_admin === 'super admin')
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard Super Admin</h1>
            </div>
        @else
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            </div>
        @endif

        <x-stat-cards :book-count="$bookCount" />

        <x-latest-books :books="$books" />

    </div>
    <!-- /.container-fluid -->
@endsection
