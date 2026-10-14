@extends('layouts.app')

@section('title', 'Upravit čtenáře')

@section('content')
    <a class="backlink" href="{{ route('readers.show', $reader) }}"><x-icon name="arrow-left" :size="15" /> {{ $reader->name }}</a>
    <div class="page-head"><h1>Upravit čtenáře</h1></div>

    <form class="panel form-panel" method="POST" action="{{ route('readers.update', $reader) }}">
        @method('PUT')
        @include('readers._form')
        <div class="form-foot">
            <button class="btn btn-primary" type="submit">Uložit změny</button>
            <a class="btn btn-ghost" href="{{ route('readers.show', $reader) }}">Zrušit</a>
        </div>
    </form>
@endsection
