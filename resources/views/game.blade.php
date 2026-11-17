@extends('partials.layout')

@section('title', 'Игра — 2048 Game')

@section('content')
    <h1 style="text-align:center; margin-bottom:24px;">Игра 2048</h1>

    <div class="game-layout">
        {{-- Игровое поле 4x4 --}}
        <div class="board" id="board">
            <div class="game-overlay hidden" id="overlay">
                <h2 id="overlay-title">Игра окончена!</h2>
                <p id="overlay-text"></p>
                <button class="btn btn-primary" onclick="Game.restart()">Играть снова</button>
            </div>
        </div>

        {{-- Панель счёта --}}
        <div class="game-info">
            <div class="card score-box">
                <div class="number" id="score">0</div>
                <div class="label">Очки</div>
            </div>
            <div class="card score-box">
                <div class="number">{{ $bestScore }}</div>
                <div class="label">Твой рекорд</div>
            </div>
            <button class="btn btn-primary" onclick="Game.restart()">🔄 Новая игра</button>
        </div>
    </div>

    <p class="game-help">
        Управление: <b>стрелки</b> на клавиатуре (на телефоне — свайпы).<br>
        Соединяй одинаковые плитки, чтобы получить 2048!
    </p>
@endsection

@push('scripts')
    <script src="{{ asset('js/game2048.js') }}"></script>
    <script>
        // Передаём в игру адрес для сохранения результата и CSRF-токен
        Game.init({
            saveUrl: "{{ route('score.submit') }}",
            csrfToken: "{{ csrf_token() }}",
        });
    </script>
@endpush
