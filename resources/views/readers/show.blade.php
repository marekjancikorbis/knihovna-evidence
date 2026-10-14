@extends('layouts.app')

@section('title', $reader->name)

@section('content')
    <a class="backlink" href="{{ route('readers.index') }}"><x-icon name="arrow-left" :size="15" /> Čtenáři</a>

    <div class="page-head">
        <div>
            <h1>{{ $reader->name }}</h1>
            <p class="sub"><x-icon name="mail" :size="15" /> <a href="mailto:{{ $reader->email }}">{{ $reader->email }}</a></p>
        </div>
        <div class="actions">
            <a class="btn" href="{{ route('readers.edit', $reader) }}"><x-icon name="pencil" :size="15" /> Upravit</a>
            <form class="inline" method="POST" action="{{ route('readers.destroy', $reader) }}"
                  onsubmit="return confirm('Smazat čtenáře {{ addslashes($reader->name) }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger" type="submit"><x-icon name="trash" :size="15" /> Smazat</button>
            </form>
            <a class="btn btn-primary" href="{{ route('loans.create', ['reader_id' => $reader->id]) }}"><x-icon name="loans" :size="16" /> Půjčit knihu</a>
        </div>
    </div>

    <div class="section-head">
        <h2>Výpůjčky</h2>
        <span class="count">{{ $activeCount }} doma · {{ $loans->total() }} celkem</span>
    </div>
    <div class="panel">
        @include('loans._table', [
            'loans' => $loans,
            'hideReader' => true,
            'empty' => 'Tento čtenář si zatím nic nepůjčil.',
        ])
    </div>
@endsection
