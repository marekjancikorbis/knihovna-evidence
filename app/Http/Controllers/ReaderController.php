<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReaderRequest;
use App\Models\Reader;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReaderController extends Controller
{
    public function index(): View
    {
        $readers = Reader::query()
            ->withCount(['loans as active_loans_count' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->paginate(15);

        return view('readers.index', compact('readers'));
    }

    public function create(): View
    {
        return view('readers.create', ['reader' => new Reader]);
    }

    public function store(ReaderRequest $request): RedirectResponse
    {
        $reader = Reader::create($request->validated());

        return redirect()->route('readers.show', $reader)->with('status', 'Čtenář byl vytvořen.');
    }

    public function show(Reader $reader): View
    {
        $loans = $reader->loans()
            ->with('book')
            ->orderByRaw('returned_at IS NOT NULL')
            ->orderByDesc('borrowed_at')
            ->paginate(20);

        $activeCount = $reader->loans()->active()->count();

        return view('readers.show', compact('reader', 'loans', 'activeCount'));
    }

    public function edit(Reader $reader): View
    {
        return view('readers.edit', compact('reader'));
    }

    public function update(ReaderRequest $request, Reader $reader): RedirectResponse
    {
        $reader->update($request->validated());

        return redirect()->route('readers.show', $reader)->with('status', 'Čtenář byl upraven.');
    }

    public function destroy(Reader $reader): RedirectResponse
    {
        if ($reader->loans()->active()->exists()) {
            return back()->withErrors(['reader' => 'Čtenáře nelze smazat, má nevrácené výpůjčky.']);
        }

        $reader->delete();

        return redirect()->route('readers.index')->with('status', 'Čtenář byl smazán.');
    }
}
