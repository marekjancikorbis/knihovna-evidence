@extends('layouts.app')

@section('title', 'Čtenáři')

@section('content')
    <div class="page-head">
        <div>
            <h1>Čtenáři</h1>
            <p class="sub">Registrovaní čtenáři a kolik knih mají právě doma.</p>
        </div>
        <div class="actions">
            <a class="btn btn-primary" href="{{ route('readers.create') }}"><x-icon name="plus" :size="16" /> Nový čtenář</a>
        </div>
    </div>

    <div class="panel">
        @if ($readers->isEmpty())
            <div class="empty">
                <div class="empty-icon"><x-icon name="users" :size="20" /></div>
                <h2>Zatím žádní čtenáři</h2>
                <p>Zaregistrujte čtenáře, abyste mu mohli půjčit knihu.</p>
                <a class="btn btn-primary" href="{{ route('readers.create') }}"><x-icon name="plus" :size="16" /> Nový čtenář</a>
            </div>
        @else
            <div class="table-scroll">
                <table class="data">
                    <thead>
                    <tr>
                        <th>Čtenář</th>
                        <th>Právě půjčeno</th>
                        <th class="right"><span class="visually-hidden">Akce</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($readers as $reader)
                        <tr>
                            <td>
                                <a class="cell-title" href="{{ route('readers.show', $reader) }}">{{ $reader->name }}</a>
                                <span class="cell-sub">{{ $reader->email }}</span>
                            </td>
                            <td>
                                @if ($reader->active_loans_count > 0)
                                    <span class="chip chip-ok num">{{ trans_choice('{1} :count kniha|[2,4] :count knihy|[5,*] :count knih', $reader->active_loans_count) }}</span>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a class="btn btn-ghost btn-icon" href="{{ route('readers.edit', $reader) }}"
                                       aria-label="Upravit {{ $reader->name }}" title="Upravit"><x-icon name="pencil" :size="16" /></a>
                                    <form class="inline" method="POST" action="{{ route('readers.destroy', $reader) }}"
                                          onsubmit="return confirm('Smazat čtenáře {{ addslashes($reader->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-ghost btn-icon btn-danger" type="submit"
                                                aria-label="Smazat {{ $reader->name }}" title="Smazat"><x-icon name="trash" :size="16" /></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if ($readers->hasPages())
                <div class="panel-foot">{{ $readers->links() }}</div>
            @endif
        @endif
    </div>
@endsection
