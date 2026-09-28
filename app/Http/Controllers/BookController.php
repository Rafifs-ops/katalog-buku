<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $books = ($request->user()->isSuperAdmin() ? Book::query() : $request->user()->books())
            ->with(['user', 'category'])
            ->paginate(5);

        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('books.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['cover_url']);
        $data['user_id'] = $request->user()->id;
        $data['cover_url'] = $request->file('cover_url')->store('covers', 'public'); // output: nama file

        Book::create($data);

        return to_route('books.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    /**
     * Show the specified book: owners are redirected to the edit form,
     * everyone else with view access (super admin) gets a read-only page.
     */
    public function show(Book $book): View|RedirectResponse
    {
        Gate::authorize('view', $book);

        if (Gate::allows('update', $book)) {
            return to_route('books.edit', $book);
        }

        return view('books.show', ['book' => $book->load(['user', 'category'])]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book): View
    {
        Gate::authorize('update', $book);

        $categories = Category::orderBy('name')->get();

        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        Gate::authorize('update', $book);

        $data = $request->safe()->except(['cover_url']);

        if ($request->hasFile('cover_url')) {
            if ($book->cover_url) {
                Storage::disk('public')->delete($book->cover_url);
            }

            $data['cover_url'] = $request->file('cover_url')->store('covers', 'public');
        }

        $book->update($data);

        return to_route('books.index')->with('success', 'Buku berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): RedirectResponse
    {
        Gate::authorize('delete', $book);

        if ($book->cover_url) {
            Storage::disk('public')->delete($book->cover_url);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus!');
    }
}
