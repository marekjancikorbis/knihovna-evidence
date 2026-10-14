@extends('layouts.app')

@section('title', $status === 'overdue' ? 'Po termínu' : 'Výpůjčky')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $status === 'overdue' ? 'Výpůjčky po termínu' : 'Výpůjčky' }}</h1>
            <p class="sub">
                @if ($status === 'overdue')
                    Knihy, které měly být vrácené a ještě nejsou. Nejstarší termín je nahoře.
                @else
                    Kdo má kterou knihu a do kdy ji má vrátit.
                @endif
            </p>
        </div>
        <div class="actions">
            <a class="btn btn-primary" href="{{ route('loans.create') }}"><x-icon name="plus" :size="16" /> Nová výpůjčka</a>
        </div>
    </div>

    <div class="panel">
        <nav class="tabs" aria-label="Filtr výpůjček">
            @foreach ($statuses as $key => $label)
                <a href="{{ route('loans.index', ['status' => $key]) }}" @if ($key === $status) aria-current="page" @endif>
                    {{ $label }}
                    <span @class(['tab-count', 'is-alert' => $key === 'overdue' && $counts['overdue'] > 0])>{{ $counts[$key] }}</span>
                </a>
            @endforeach
        </nav>

        @include('loans._table', [
            'loans' => $loans,
            'empty' => match ($status) {
                'overdue' => 'Všechny knihy jsou vrácené včas nebo ještě nevypršel jejich termín.',
                'returned' => 'Zatím nebyla vrácena žádná kniha.',
                default => null,
            },
        ])
    </div>
@endsection
