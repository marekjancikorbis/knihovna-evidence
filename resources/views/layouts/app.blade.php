<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Knihovna') · Knihovna</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible+Next:wght@400..700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@php
    $onOverdue = request()->routeIs('loans.index') && request('status') === 'overdue';
@endphp
<header class="topbar">
    <div class="topbar-inner">
        <a class="brand" href="{{ route('books.index') }}">
            <span class="brand-mark"><x-icon name="library" :size="17" /></span>
            <span class="brand-name">Knihovna</span>
        </a>
        <nav class="mainnav" aria-label="Hlavní navigace">
            <a href="{{ route('books.index') }}" @if (request()->routeIs('books.*')) aria-current="page" @endif>
                <x-icon name="book" :size="17" /> <span class="nav-label">Knihy</span>
            </a>
            <a href="{{ route('readers.index') }}" @if (request()->routeIs('readers.*')) aria-current="page" @endif>
                <x-icon name="users" :size="17" /> <span class="nav-label">Čtenáři</span>
            </a>
            <a href="{{ route('loans.index') }}" @if (request()->routeIs('loans.*') && ! $onOverdue) aria-current="page" @endif>
                <x-icon name="loans" :size="17" /> <span class="nav-label">Výpůjčky</span>
            </a>
            <a href="{{ route('loans.index', ['status' => 'overdue']) }}" @if ($onOverdue) aria-current="page" @endif>
                <x-icon name="clock" :size="17" /> <span class="nav-label">Po termínu</span>
                @if ($overdueCount > 0)
                    <span class="nav-count" aria-label="{{ $overdueCount }} po termínu">{{ $overdueCount }}</span>
                @endif
            </a>
        </nav>
    </div>
</header>

<main class="page">
    @if (session('status'))
        <div class="flash flash-ok" role="status">
            <x-icon name="check" /> <span>{{ session('status') }}</span>
        </div>
    @endif
    @foreach (['book', 'reader', 'loan'] as $key)
        @error($key)
            <div class="flash flash-err" role="alert">
                <x-icon name="alert" /> <span>{{ $message }}</span>
            </div>
        @enderror
    @endforeach

    @yield('content')
</main>
</body>
</html>
