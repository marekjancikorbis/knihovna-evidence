<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Krakatit',
            'author' => 'Karel Čapek',
            'isbn' => '9788020704472',
            'available_copies' => 3,
        ], $overrides);
    }

    public function test_book_can_be_created(): void
    {
        $this->post(route('books.store'), $this->validData())->assertRedirect();

        $this->assertDatabaseHas('books', ['isbn' => '9788020704472', 'available_copies' => 3]);
    }

    public function test_isbn_with_hyphens_is_normalized(): void
    {
        $this->post(route('books.store'), $this->validData(['isbn' => '978-80-207-0447-2']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('books', ['isbn' => '9788020704472']);
    }

    public function test_isbn_must_have_13_characters(): void
    {
        $this->post(route('books.store'), $this->validData(['isbn' => '978802070447']))
            ->assertSessionHasErrors('isbn');
        $this->post(route('books.store'), $this->validData(['isbn' => '97880207044721']))
            ->assertSessionHasErrors('isbn');

        $this->assertDatabaseCount('books', 0);
    }

    public function test_isbn_must_be_unique(): void
    {
        Book::factory()->create(['isbn' => '9788020704472']);

        $this->post(route('books.store'), $this->validData())->assertSessionHasErrors('isbn');
    }

    public function test_book_can_keep_its_own_isbn_on_update(): void
    {
        $book = Book::factory()->create(['isbn' => '9788020704472']);

        $this->put(route('books.update', $book), $this->validData(['title' => 'Nový název']))
            ->assertSessionHasNoErrors();

        $this->assertSame('Nový název', $book->fresh()->title);
    }

    public function test_available_copies_must_be_non_negative_integer(): void
    {
        $this->post(route('books.store'), $this->validData(['available_copies' => -1]))
            ->assertSessionHasErrors('available_copies');
        $this->post(route('books.store'), $this->validData(['available_copies' => 1.5]))
            ->assertSessionHasErrors('available_copies');
        $this->post(route('books.store'), $this->validData(['available_copies' => 'abc']))
            ->assertSessionHasErrors('available_copies');

        $this->post(route('books.store'), $this->validData(['available_copies' => 0]))
            ->assertSessionHasNoErrors();
    }

    public function test_index_filters_by_title_or_author(): void
    {
        Book::factory()->create(['title' => 'Krakatit', 'author' => 'Karel Čapek']);
        Book::factory()->create(['title' => 'Babička', 'author' => 'Božena Němcová']);
        Book::factory()->create(['title' => 'Proces', 'author' => 'Franz Kafka']);

        $this->get(route('books.index', ['q' => 'krak']))
            ->assertOk()->assertSee('Krakatit')->assertDontSee('Babička')->assertDontSee('Proces');

        $this->get(route('books.index', ['q' => 'Kafka']))
            ->assertOk()->assertSee('Proces')->assertDontSee('Krakatit');
    }

    public function test_detail_shows_loans(): void
    {
        $loan = Loan::factory()->create();

        $this->get(route('books.show', $loan->book))
            ->assertOk()
            ->assertSee($loan->reader->name);
    }

    public function test_book_can_be_deleted(): void
    {
        $book = Book::factory()->create();

        $this->delete(route('books.destroy', $book))->assertRedirect(route('books.index'));

        $this->assertModelMissing($book);
    }

    public function test_book_with_active_loan_cannot_be_deleted(): void
    {
        $loan = Loan::factory()->create();

        $this->delete(route('books.destroy', $loan->book))->assertSessionHasErrors('book');

        $this->assertModelExists($loan->book);
    }
}
