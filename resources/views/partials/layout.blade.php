{{-- Общий шаблон всех страниц: шапка + содержимое + подвал --}}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '2048 Game')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    {{-- Шапка: логотип + меню + кнопки входа/профиля --}}
    <header>
        <div class="header-inner">
            <a href="{{ route('home') }}" class="logo">🎮 2048 Game</a>

            <nav>
                <a href="{{ route('home') }}">Главная</a>
                <a href="{{ route('leaderboard') }}">Рекорды</a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin') }}">Админ-панель</a>
                    @endif

                    <a href="{{ route('game') }}">Играть</a>
                    <span class="role-badge">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>

                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline">Выйти</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Вход</a>
                    <a href="{{ route('register') }}">Регистрация</a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Центральная часть: содержимое страницы вставляется сюда --}}
    <main>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    <footer>2048 Game · Laravel + PostgreSQL · учебный проект</footer>

    @stack('scripts')
</body>
</html>
