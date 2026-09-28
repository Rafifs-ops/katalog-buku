<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\View\View;

class KatalogController extends Controller
{
    /**
     * Display the public book catalog.
     */
    public function index(): View
    {
        $books = Book::with(['user', 'category'])->latest()->paginate(9);

        return view('katalog.index', compact('books'));
    }

    /**
     * Display the public detail page of a book.
     */
    public function show(Book $book): View
    {
        $book->load(['user', 'category']);

        return view('katalog.show', compact('book'));
    }
}
