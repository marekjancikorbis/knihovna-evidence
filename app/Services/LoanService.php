<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    /**
     * Create a loan and take one copy of the book off the shelf.
     *
     * The book row is locked for the duration of the transaction so two concurrent
     * requests cannot both take the last copy or both lend the same book to one reader.
     *
     * @throws ValidationException
     */
    public function borrow(int $bookId, int $readerId, Carbon $borrowedAt, Carbon $dueAt): Loan
    {
        return DB::transaction(function () use ($bookId, $readerId, $borrowedAt, $dueAt) {
            $book = Book::query()->lockForUpdate()->findOrFail($bookId);

            if (! $book->isAvailable()) {
                throw ValidationException::withMessages([
                    'book_id' => 'Kniha nemá žádný dostupný výtisk.',
                ]);
            }

            $alreadyBorrowed = Loan::query()
                ->active()
                ->where('book_id', $bookId)
                ->where('reader_id', $readerId)
                ->exists();

            if ($alreadyBorrowed) {
                throw ValidationException::withMessages([
                    'book_id' => 'Čtenář má tuto knihu již vypůjčenou a dosud ji nevrátil.',
                ]);
            }

            $book->decrement('available_copies');

            return Loan::create([
                'book_id' => $bookId,
                'reader_id' => $readerId,
                'borrowed_at' => $borrowedAt,
                'due_at' => $dueAt,
            ]);
        });
    }

    /**
     * Mark the loan as returned and put the copy back on the shelf.
     *
     * @throws ValidationException
     */
    public function return(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan) {
            $loan = Loan::query()->lockForUpdate()->findOrFail($loan->id);

            if ($loan->isReturned()) {
                throw ValidationException::withMessages([
                    'loan' => 'Výpůjčka již byla vrácena.',
                ]);
            }

            $loan->update(['returned_at' => now()]);
            Book::query()->whereKey($loan->book_id)->increment('available_copies');

            return $loan;
        });
    }
}
