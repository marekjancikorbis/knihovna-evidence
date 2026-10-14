@extends('layouts.app')

@section('title', 'Nový čtenář')

@section('content')
    <a class="backlink" href="{{ route('readers.index') }}"><x-icon name="arrow-left" :size="15" /> Čtenáři</a>
    <div class="page-head"><h1>Nový čtenář</h1></div>

    <form class="panel form-panel" method="POST" action="{{ route('readers.store') }}">
        @include('readers._form')
        <div class="form-foot">
            <button class="btn btn-primary" type="submit">Přidat čtenáře</button>
            <a class="btn btn-ghost" href="{{ route('readers.index') }}">Zrušit</a>
        </div>
    </form>
@endsection
