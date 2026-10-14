{{-- Expects $loans; optional $hideBook / $hideReader drop a column on detail pages; $empty overrides the empty-state text. --}}
@if ($loans->isEmpty())
    <div class="empty">
        <div class="empty-icon"><x-icon name="loans" :size="20" /></div>
        <h2>Žádné výpůjčky</h2>
        <p>{{ $empty ?? 'Jakmile si čtenář knihu vypůjčí, objeví se tady i s termínem vrácení.' }}</p>
    </div>
@else
    <div class="table-scroll">
        <table class="data">
            <thead>
            <tr>
                @unless ($hideBook ?? false)<th>Kniha</th>@endunless
                @unless ($hideReader ?? false)<th>Čtenář</th>@endunless
                <th>Vypůjčeno</th>
                <th>Termín vrácení</th>
                <th>Stav</th>
                <th class="right"><span class="visually-hidden">Akce</span></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($loans as $loan)
                <tr @class(['is-overdue' => $loan->isOverdue()])>
                    @unless ($hideBook ?? false)
                        <td>
                            <a class="cell-title" href="{{ route('books.show', $loan->book) }}">{{ $loan->book->title }}</a>
                            <span class="cell-sub">{{ $loan->book->author }}</span>
                        </td>
                    @endunless
                    @unless ($hideReader ?? false)
                        <td>
                            <a class="cell-title" href="{{ route('readers.show', $loan->reader) }}">{{ $loan->reader->name }}</a>
                            <span class="cell-sub">{{ $loan->reader->email }}</span>
                        </td>
                    @endunless
                    <td class="nowrap">{{ $loan->borrowed_at->format('j. n. Y') }}</td>
                    <td class="nowrap">{{ $loan->due_at->format('j. n. Y') }}</td>
                    <td>
                        @if ($loan->isReturned())
                            <span class="chip chip-neutral">Vráceno {{ $loan->returned_at->format('j. n.') }}</span>
                        @elseif ($loan->isOverdue())
                            @php $days = max(1, (int) $loan->due_at->diffInDays(now())); @endphp
                            <span class="chip chip-danger">{{ trans_choice('{1} :count den po termínu|[2,4] :count dny po termínu|[5,*] :count dní po termínu', $days) }}</span>
                        @else
                            <span class="chip chip-ok">Vypůjčeno</span>
                        @endif
                    </td>
                    <td class="right">
                        @unless ($loan->isReturned())
                            <form class="inline" method="POST" action="{{ route('loans.return', $loan) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm" type="submit"><x-icon name="return" :size="15" /> Vrátit</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if ($loans->hasPages())
        <div class="panel-foot">{{ $loans->links() }}</div>
    @endif
@endif
