<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $books = Book::query()
            ->search($search)
            ->withCount(['loans as active_loans_count' => fn ($q) => $q->active()])
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('books.index', compact('books', 'search'));
    }

    public function create(): View
    {
        return view('books.create', ['book' => new Book]);
    }

    public function store(BookRequest $request): RedirectResponse
    {
        $book = Book::create($request->validated());

        return redirect()->route('books.show', $book)->with('status', 'Kniha byla vytvořena.');
    }

    public function show(Book $book): View
    {
        $loans = $book->loans()
            ->with('reader')
            ->orderByRaw('returned_at IS NOT NULL')
            ->orderByDesc('borrowed_at')
            ->paginate(20);

        $activeCount = $book->loans()->active()->count();

        return view('books.show', compact('book', 'loans', 'activeCount'));
    }

    public function edit(Book $book): View
    {
        return view('books.edit', compact('book'));
    }

    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        $book->update($request->validated());

        return redirect()->route('books.show', $book)->with('status', 'Kniha byla upravena.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->loans()->active()->exists()) {
            return back()->withErrors(['book' => 'Knihu nelze smazat, má nevrácené výpůjčky.']);
        }

        $book->delete();

        return redirect()->route('books.index')->with('status', 'Kniha byla smazána.');
    }
}
