<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReaderController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/books');

Route::resource('books', BookController::class);
Route::resource('readers', ReaderController::class);

Route::redirect('loans/overdue', '/loans?status=overdue')->name('loans.overdue');
Route::patch('loans/{loan}/return', [LoanController::class, 'return'])->name('loans.return');
Route::resource('loans', LoanController::class)->only(['index', 'create', 'store']);
