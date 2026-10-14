@extends('layouts.app')

@section('title', 'Knihy')

@section('content')
    <div class="page-head">
        <div>
            <h1>Knihy</h1>
            <p class="sub">Katalog titulů a počet výtisků, které jsou právě v regálu.</p>
        </div>
        <div class="actions">
            <a class="btn btn-primary" href="{{ route('books.create') }}"><x-icon name="plus" :size="16" /> Nová kniha</a>
        </div>
    </div>

    <div class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('books.index') }}" class="search" role="search">
                <x-icon name="search" :size="16" />
                <label for="q" class="visually-hidden">Hledat knihy</label>
                <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Název nebo autor…">
                <button class="btn" type="submit">Hledat</button>
            </form>
            <div class="meta num">
                {{ trans_choice('{0} Žádná kniha|{1} :count kniha|[2,4] :count knihy|[5,*] :count knih', $books->total()) }}
                @if ($search !== '')
                    · <a href="{{ route('books.index') }}">zrušit filtr</a>
                @endif
            </div>
        </div>

        @if ($books->isEmpty())
            <div class="empty">
                <div class="empty-icon"><x-icon name="{{ $search !== '' ? 'search' : 'book' }}" :size="20" /></div>
                @if ($search !== '')
                    <h2>Nic neodpovídá „{{ $search }}“</h2>
                    <p>Zkuste jen část názvu nebo příjmení autora.</p>
                    <a class="btn" href="{{ route('books.index') }}"><x-icon name="x" :size="15" /> Zrušit filtr</a>
                @else
                    <h2>Katalog je prázdný</h2>
                    <p>Přidejte první titul. Pak ho půjde půjčovat čtenářům.</p>
                    <a class="btn btn-primary" href="{{ route('books.create') }}"><x-icon name="plus" :size="16" /> Nová kniha</a>
                @endif
            </div>
        @else
            <div class="table-scroll">
                <table class="data">
                    <thead>
                    <tr>
                        <th>Titul</th>
                        <th class="hide-sm">ISBN</th>
                        <th>V regálu</th>
                        <th class="right hide-sm">Půjčeno</th>
                        <th class="right"><span class="visually-hidden">Akce</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($books as $book)
                        <tr>
                            <td>
                                <a class="cell-title" href="{{ route('books.show', $book) }}">{{ $book->title }}</a>
                                <span class="cell-sub">{{ $book->author }}</span>
                            </td>
                            <td class="muted hide-sm">{{ $book->isbn }}</td>
                            <td>
                                @if ($book->isAvailable())
                                    <span class="chip chip-ok num">{{ $book->available_copies }} k dispozici</span>
                                @else
                                    <span class="chip chip-danger">Vše půjčeno</span>
                                @endif
                            </td>
                            <td class="right num hide-sm">{{ $book->active_loans_count }}</td>
                            <td>
                                <div class="row-actions">
                                    <a class="btn btn-ghost btn-icon" href="{{ route('books.edit', $book) }}"
                                       aria-label="Upravit {{ $book->title }}" title="Upravit"><x-icon name="pencil" :size="16" /></a>
                                    <form class="inline" method="POST" action="{{ route('books.destroy', $book) }}"
                                          onsubmit="return confirm('Smazat knihu „{{ addslashes($book->title) }}“?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-ghost btn-icon btn-danger" type="submit"
                                                aria-label="Smazat {{ $book->title }}" title="Smazat"><x-icon name="trash" :size="16" /></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if ($books->hasPages())
                <div class="panel-foot">{{ $books->links() }}</div>
            @endif
        @endif
    </div>
@endsection
