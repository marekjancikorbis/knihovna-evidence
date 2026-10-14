@extends('layouts.app')

@section('title', 'Upravit knihu')

@section('content')
    <a class="backlink" href="{{ route('books.show', $book) }}"><x-icon name="arrow-left" :size="15" /> {{ $book->title }}</a>
    <div class="page-head"><h1>Upravit knihu</h1></div>

    <form class="panel form-panel" method="POST" action="{{ route('books.update', $book) }}">
        @method('PUT')
        @include('books._form')
        <div class="form-foot">
            <button class="btn btn-primary" type="submit">Uložit změny</button>
            <a class="btn btn-ghost" href="{{ route('books.show', $book) }}">Zrušit</a>
        </div>
    </form>
@endsection
