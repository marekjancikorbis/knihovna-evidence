@extends('layouts.app')

@section('title', $book->title)

@section('content')
    <a class="backlink" href="{{ route('books.index') }}"><x-icon name="arrow-left" :size="15" /> Knihy</a>

    <div class="page-head">
        <div>
            <h1>{{ $book->title }}</h1>
            <p class="sub">{{ $book->author }}</p>
        </div>
        <div class="actions">
            <a class="btn" href="{{ route('books.edit', $book) }}"><x-icon name="pencil" :size="15" /> Upravit</a>
            <form class="inline" method="POST" action="{{ route('books.destroy', $book) }}"
                  onsubmit="return confirm('Smazat knihu „{{ addslashes($book->title) }}“?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger" type="submit"><x-icon name="trash" :size="15" /> Smazat</button>
            </form>
            @if ($book->isAvailable())
                <a class="btn btn-primary" href="{{ route('loans.create', ['book_id' => $book->id]) }}"><x-icon name="loans" :size="16" /> Půjčit</a>
            @else
                <button class="btn btn-primary" type="button" disabled title="Žádný výtisk není v regálu">Půjčit</button>
            @endif
        </div>
    </div>

    <dl class="panel facts">
        <div>
            <dt>V regálu</dt>
            <dd>
                @if ($book->isAvailable())
                    <span class="chip chip-ok">{{ $book->available_copies }} k dispozici</span>
                @else
                    <span class="chip chip-danger">Vše půjčeno</span>
                @endif
            </dd>
        </div>
        <div><dt>Právě půjčeno</dt><dd>{{ $activeCount }}</dd></div>
        <div><dt>ISBN</dt><dd>{{ $book->isbn }}</dd></div>
    </dl>

    <div class="section-head">
        <h2>Výpůjčky</h2>
        <span class="count">{{ $loans->total() }}</span>
    </div>
    <div class="panel">
        @include('loans._table', [
            'loans' => $loans,
            'hideBook' => true,
            'empty' => 'Tuhle knihu si zatím nikdo nepůjčil.',
        ])
    </div>
@endsection
