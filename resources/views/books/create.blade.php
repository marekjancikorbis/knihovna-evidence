@extends('layouts.app')

@section('title', 'Nová kniha')

@section('content')
    <a class="backlink" href="{{ route('books.index') }}"><x-icon name="arrow-left" :size="15" /> Knihy</a>
    <div class="page-head"><h1>Nová kniha</h1></div>

    <form class="panel form-panel" method="POST" action="{{ route('books.store') }}">
        @include('books._form')
        <div class="form-foot">
            <button class="btn btn-primary" type="submit">Přidat knihu</button>
            <a class="btn btn-ghost" href="{{ route('books.index') }}">Zrušit</a>
        </div>
    </form>
@endsection
