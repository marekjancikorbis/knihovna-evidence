<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_can_be_created(): void
    {
        $this->post(route('readers.store'), ['name' => 'Jan Novák', 'email' => 'jan@example.com'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('readers', ['email' => 'jan@example.com']);
    }

    public function test_email_is_required_and_valid(): void
    {
        $this->post(route('readers.store'), ['name' => 'Jan Novák', 'email' => 'nope'])
            ->assertSessionHasErrors('email');
    }

    public function test_reader_with_active_loan_cannot_be_deleted(): void
    {
        $loan = Loan::factory()->create();

        $this->delete(route('readers.destroy', $loan->reader))->assertSessionHasErrors('reader');

        $this->assertModelExists($loan->reader);
    }

    public function test_reader_detail_lists_loans(): void
    {
        $loan = Loan::factory()->create();

        $this->get(route('readers.show', $loan->reader))->assertOk()->assertSee($loan->book->title);
    }

    public function test_all_pages_render(): void
    {
        Loan::factory()->overdue()->create();
        $reader = Reader::first();

        foreach ([
            route('books.index'), route('books.create'),
            route('readers.index'), route('readers.create'), route('readers.edit', $reader),
            route('loans.index'), route('loans.index', ['status' => 'all']),
            route('loans.index', ['status' => 'returned']), route('loans.create'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
