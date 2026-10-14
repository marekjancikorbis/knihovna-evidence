@extends('layouts.app')

@section('title', 'Nová výpůjčka')

@section('content')
    <a class="backlink" href="{{ route('loans.index') }}"><x-icon name="arrow-left" :size="15" /> Výpůjčky</a>
    <div class="page-head">
        <div>
            <h1>Nová výpůjčka</h1>
            <p class="sub">V nabídce jsou jen knihy, které mají výtisk v regálu.</p>
        </div>
    </div>

    @if ($books->isEmpty() || $readers->isEmpty())
        <div class="flash flash-err" role="alert">
            <x-icon name="alert" />
            <span>
                @if ($books->isEmpty()) Žádná kniha teď nemá volný výtisk. @endif
                @if ($readers->isEmpty()) Nejdřív <a href="{{ route('readers.create') }}">zaregistrujte čtenáře</a>. @endif
            </span>
        </div>
    @endif

    <form class="panel form-panel" method="POST" action="{{ route('loans.store') }}">
        @csrf
        <div class="form-body">
            <div class="field">
                <label for="book_id">Kniha</label>
                <select id="book_id" name="book_id" required
                        @error('book_id') aria-invalid="true" aria-describedby="book_id-error" @enderror>
                    <option value="">Vyberte knihu…</option>
                    @foreach ($books as $book)
                        <option value="{{ $book->id }}" @selected((int) old('book_id', $selectedBook) === $book->id)>
                            {{ $book->title }} — {{ $book->author }} ({{ $book->available_copies }} v regálu)
                        </option>
                    @endforeach
                </select>
                @error('book_id')<div class="error" id="book_id-error"><x-icon name="alert" :size="14" /> {{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="reader_id">Čtenář</label>
                <select id="reader_id" name="reader_id" required
                        @error('reader_id') aria-invalid="true" aria-describedby="reader_id-error" @enderror>
                    <option value="">Vyberte čtenáře…</option>
                    @foreach ($readers as $reader)
                        <option value="{{ $reader->id }}" @selected((int) old('reader_id', $selectedReader) === $reader->id)>
                            {{ $reader->name }} ({{ $reader->email }})
                        </option>
                    @endforeach
                </select>
                @error('reader_id')<div class="error" id="reader_id-error"><x-icon name="alert" :size="14" /> {{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <x-input name="borrowed_at" label="Vypůjčeno dne" type="date" :value="now()->toDateString()" required />
                <x-input name="due_at" label="Vrátit do" type="date" :value="now()->addDays(30)->toDateString()" required
                         hint="Výchozí výpůjční doba je 30 dní." />
            </div>
        </div>
        <div class="form-foot">
            <button class="btn btn-primary" type="submit" @disabled($books->isEmpty() || $readers->isEmpty())>Půjčit</button>
            <a class="btn btn-ghost" href="{{ url()->previous() }}">Zrušit</a>
        </div>
    </form>
@endsection
