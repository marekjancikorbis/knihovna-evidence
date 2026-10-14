<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Reader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with demo library data.
     */
    public function run(): void
    {
        $books = collect([
            ['Krakatit', 'Karel Čapek', '9788020704472', 3],
            ['Válka s mloky', 'Karel Čapek', '9788020710947', 2],
            ['Babička', 'Božena Němcová', '9788073902139', 4],
            ['Osudy dobrého vojáka Švejka', 'Jaroslav Hašek', '9788020702584', 2],
            ['Saturnin', 'Zdeněk Jirotka', '9788020708296', 1],
            ['Proces', 'Franz Kafka', '9788020711494', 2],
        ])->map(fn ($b) => Book::create([
            'title' => $b[0], 'author' => $b[1], 'isbn' => $b[2], 'available_copies' => $b[3],
        ]));

        $readers = Reader::factory(5)->create();

        // A few loans – some active, one overdue, one returned. Copies are taken off the shelf to match.
        $this->loan($books[0], $readers[0], borrowedDaysAgo: 5, dueInDays: 25);
        $this->loan($books[1], $readers[1], borrowedDaysAgo: 40, dueInDays: -10);
        $this->loan($books[4], $readers[2], borrowedDaysAgo: 20, dueInDays: -3);
        Loan::create([
            'book_id' => $books[2]->id,
            'reader_id' => $readers[0]->id,
            'borrowed_at' => now()->subDays(60),
            'due_at' => now()->subDays(30),
            'returned_at' => now()->subDays(32),
        ]);
    }

    private function loan(Book $book, Reader $reader, int $borrowedDaysAgo, int $dueInDays): void
    {
        Loan::create([
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'borrowed_at' => now()->subDays($borrowedDaysAgo),
            'due_at' => now()->addDays($dueInDays),
        ]);
        $book->decrement('available_copies');
    }
}
