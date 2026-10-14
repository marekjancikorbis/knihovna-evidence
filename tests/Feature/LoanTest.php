<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    private function loanData(Book $book, Reader $reader, array $overrides = []): array
    {
        return array_merge([
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'borrowed_at' => now()->toDateString(),
            'due_at' => now()->addDays(14)->toDateString(),
        ], $overrides);
    }

    public function test_loan_is_created_and_copy_is_taken(): void
    {
        $book = Book::factory()->create(['available_copies' => 2]);
        $reader = Reader::factory()->create();

        $this->post(route('loans.store'), $this->loanData($book, $reader))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('loans', ['book_id' => $book->id, 'reader_id' => $reader->id, 'returned_at' => null]);
        $this->assertSame(1, $book->fresh()->available_copies);
    }

    public function test_loan_is_rejected_when_no_copy_is_available(): void
    {
        $book = Book::factory()->create(['available_copies' => 0]);
        $reader = Reader::factory()->create();

        $this->post(route('loans.store'), $this->loanData($book, $reader))
            ->assertSessionHasErrors('book_id');

        $this->assertDatabaseCount('loans', 0);
        $this->assertSame(0, $book->fresh()->available_copies);
    }

    public function test_due_at_must_be_after_borrowed_at(): void
    {
        $book = Book::factory()->create();
        $reader = Reader::factory()->create();

        $this->post(route('loans.store'), $this->loanData($book, $reader, [
            'borrowed_at' => '2026-10-07',
            'due_at' => '2026-10-07',
        ]))->assertSessionHasErrors('due_at');

        $this->post(route('loans.store'), $this->loanData($book, $reader, [
            'borrowed_at' => '2026-10-07',
            'due_at' => '2026-10-01',
        ]))->assertSessionHasErrors('due_at');

        $this->assertDatabaseCount('loans', 0);
    }

    public function test_same_book_cannot_be_lent_twice_to_same_reader_until_returned(): void
    {
        $book = Book::factory()->create(['available_copies' => 5]);
        $reader = Reader::factory()->create();

        $this->post(route('loans.store'), $this->loanData($book, $reader))->assertSessionHasNoErrors();
        $this->post(route('loans.store'), $this->loanData($book, $reader))->assertSessionHasErrors('book_id');

        $this->assertDatabaseCount('loans', 1);
        $this->assertSame(4, $book->fresh()->available_copies);

        // After returning it, the reader may borrow it again.
        $this->patch(route('loans.return', Loan::first()))->assertSessionHasNoErrors();
        $this->post(route('loans.store'), $this->loanData($book, $reader))->assertSessionHasNoErrors();

        $this->assertDatabaseCount('loans', 2);
    }

    public function test_same_book_can_be_lent_to_different_readers(): void
    {
        $book = Book::factory()->create(['available_copies' => 2]);

        $this->post(route('loans.store'), $this->loanData($book, Reader::factory()->create()))->assertSessionHasNoErrors();
        $this->post(route('loans.store'), $this->loanData($book, Reader::factory()->create()))->assertSessionHasNoErrors();

        $this->assertSame(0, $book->fresh()->available_copies);
    }

    public function test_returning_a_book_sets_returned_at_and_restores_copy(): void
    {
        $book = Book::factory()->create(['available_copies' => 0]);
        $loan = Loan::factory()->for($book)->create();

        $this->patch(route('loans.return', $loan))->assertSessionHasNoErrors();

        $this->assertNotNull($loan->fresh()->returned_at);
        $this->assertSame(1, $book->fresh()->available_copies);
    }

    public function test_loan_cannot_be_returned_twice(): void
    {
        $book = Book::factory()->create(['available_copies' => 1]);
        $loan = Loan::factory()->for($book)->returned()->create();

        $this->patch(route('loans.return', $loan))->assertSessionHasErrors('loan');

        $this->assertSame(1, $book->fresh()->available_copies);
    }

    public function test_overdue_list_shows_only_unreturned_loans_past_due(): void
    {
        $overdue = Loan::factory()->overdue()->create();
        $onTime = Loan::factory()->create();
        $returnedLate = Loan::factory()->overdue()->returned()->create();

        $this->get(route('loans.index', ['status' => 'overdue']))
            ->assertOk()
            ->assertSee($overdue->book->title)
            ->assertDontSee($onTime->book->title)
            ->assertDontSee($returnedLate->book->title);
    }

    public function test_overdue_shortcut_redirects_to_filtered_list(): void
    {
        $this->get(route('loans.overdue'))->assertRedirect('/loans?status=overdue');
    }
}
