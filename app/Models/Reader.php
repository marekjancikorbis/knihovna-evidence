<?php

namespace App\Models;

use Database\Factories\ReaderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email'])]
class Reader extends Model
{
    /** @use HasFactory<ReaderFactory> */
    use HasFactory;

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
