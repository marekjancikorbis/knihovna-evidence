<?php

namespace App\Providers;

use App\Models\Loan;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The app uses plain CSS (no Tailwind build); the bootstrap-4 markup is a plain ul.pagination styled in the layout.
        Paginator::useBootstrapFour();

        View::composer('layouts.app', function ($view) {
            $view->with('overdueCount', Loan::query()->overdue()->count());
        });
    }
}
