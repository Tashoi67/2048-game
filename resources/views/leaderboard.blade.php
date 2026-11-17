@extends('partials.layout')

@section('title', 'Рекорды — 2048 Game')

@section('content')
    <h1 style="margin-bottom:24px;">🏆 Таблица рекордов</h1>

    <div class="card">
        @if ($scores->isEmpty())
            <p style="color:#c8c8d0;">Рекордов пока нет. <a href="{{ route('game') }}" style="color:#ffd166;">Сыграй первым!</a></p>
        @else
            <div class="table-wrap">
                <table>
                <tr>
                    <th>Место</th><th>Игрок</th><th>Очки</th><th>Макс. плитка</th><th>Дата</th>
                </tr>
                    @foreach ($scores as $i => $score)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $score->user->name }}</td>
                            <td>{{ number_format($score->points, 0, ',', ' ') }}</td>
                            <td>{{ $score->best_tile }}</td>
                            <td style="color:#c8c8d0;">{{ $score->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    </div>
@endsection
