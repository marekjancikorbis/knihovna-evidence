<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Reader;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LoanController extends Controller
{
    public const STATUSES = [
        'active' => 'Aktivní',
        'overdue' => 'Po termínu',
        'returned' => 'Vrácené',
        'all' => 'Všechny',
    ];

    public function __construct(private readonly LoanService $loans) {}

    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'active');
        if (! array_key_exists($status, self::STATUSES)) {
            $status = 'active';
        }

        $query = Loan::query()->with(['book', 'reader']);

        match ($status) {
            'active' => $query->active()->orderBy('due_at'),
            'overdue' => $query->overdue()->orderBy('due_at'),
            'returned' => $query->whereNotNull('returned_at')->orderByDesc('returned_at'),
            'all' => $query->orderByDesc('borrowed_at'),
        };

        return view('loans.index', [
            'loans' => $query->paginate(20)->withQueryString(),
            'status' => $status,
            'statuses' => self::STATUSES,
            'counts' => [
                'active' => Loan::query()->active()->count(),
                'overdue' => Loan::query()->overdue()->count(),
                'returned' => Loan::query()->whereNotNull('returned_at')->count(),
                'all' => Loan::query()->count(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        return view('loans.create', [
            'books' => Book::query()->where('available_copies', '>', 0)->orderBy('title')->get(),
            'readers' => Reader::query()->orderBy('name')->get(),
            'selectedBook' => $request->integer('book_id') ?: null,
            'selectedReader' => $request->integer('reader_id') ?: null,
        ]);
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $loan = $this->loans->borrow(
            (int) $data['book_id'],
            (int) $data['reader_id'],
            Carbon::parse($data['borrowed_at']),
            Carbon::parse($data['due_at']),
        );

        return redirect()->route('books.show', $loan->book_id)->with('status', 'Výpůjčka byla založena.');
    }

    public function return(Loan $loan): RedirectResponse
    {
        $this->loans->return($loan);

        return back()->with('status', 'Kniha byla vrácena.');
    }
}
