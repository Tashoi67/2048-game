@extends('partials.layout')

@section('content')
    <div class="hero">
        <h1>2048</h1>
        <p>Сдвигай плитки стрелками и соединяй одинаковые числа, чтобы получить 2048.</p>

        @auth
            <a href="{{ route('game') }}" class="btn btn-primary">Играть</a>
        @else
            <a href="{{ route('register') }}" class="btn btn-primary">Зарегистрироваться и играть</a>
            <p style="margin-top:10px;">
                Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a>
            </p>
        @endauth
    </div>

    <div class="card">
        <h2 style="margin-bottom:15px;">Топ-5 игроков</h2>

        @if ($topScores->isEmpty())
            <p style="color:#666;">Рекордов пока нет — стань первым!</p>
        @else
            <table>
                <tr><th>Место</th><th>Игрок</th><th>Очки</th><th>Макс. плитка</th></tr>
                @foreach ($topScores as $i => $score)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $score->user->name }}</td>
                        <td>{{ number_format($score->points, 0, ',', ' ') }}</td>
                        <td>{{ $score->best_tile }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection
