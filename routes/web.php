<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);
});

Route::get('/', [KatalogController::class, 'index'])->name('katalog.index');

/*
|--------------------------------------------------------------------------
| Admin routes (protected by auth)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::resource('books', BookController::class);
    Route::resource('categories', CategoryController::class);

    Route::get('/dashboard', function (Request $request) {
        $bookCount = $request->user()->books()->count();
        $is_admin = Auth::user()->role;

        $books = $request->user()->books()->with('category')->latest()->get();

        return view('dashboard.index', compact('bookCount', 'books', 'is_admin'));
    })->name('admin');
});

Route::get('/{book}', [KatalogController::class, 'show'])->name('katalog.show');
