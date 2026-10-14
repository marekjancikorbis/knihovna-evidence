<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Reader;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'reader_id' => Reader::factory(),
            'borrowed_at' => now()->subDays(7),
            'due_at' => now()->addDays(14),
            'returned_at' => null,
        ];
    }

    public function overdue(): static
    {
        return $this->state([
            'borrowed_at' => now()->subDays(30),
            'due_at' => now()->subDays(2),
        ]);
    }

    public function returned(): static
    {
        return $this->state(['returned_at' => now()]);
    }
}
