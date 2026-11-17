@extends('partials.layout')

@section('title', 'Админ-панель — 2048 Game')

@section('content')
    <h1 style="margin-bottom:24px;">⚙️ Админ-панель</h1>

    {{-- Статистика сайта --}}
    <div class="stats-row">
        <div class="card stat-card">
            <div class="number">{{ $stats['users'] }}</div>
            <div class="label">Пользователей</div>
        </div>
        <div class="card stat-card">
            <div class="number">{{ $stats['games'] }}</div>
            <div class="label">Игр сыграно</div>
        </div>
        <div class="card stat-card">
            <div class="number">{{ number_format($stats['best_score'], 0, ',', ' ') }}</div>
            <div class="label">Лучший счёт</div>
        </div>
    </div>

    <div class="card">
        <h2 class="section-title" style="margin-top:0;">👥 Пользователи</h2>

        <div class="table-wrap">
            <table>
                <tr>
                    <th>Логин</th>
                    <th>Роль</th>
                    <th>Рекорд</th>
                    <th>Игр</th>
                    <th>Регистрация</th>
                    <th>Действия</th>
                </tr>

                @foreach ($users as $user)
                    <tr>
                        <td><b>{{ $user->name }}</b></td>
                        <td>
                            <b>{{ $user->role }}</b>
                        </td>
                        <td>{{ number_format($user->bestScore(), 0, ',', ' ') }}</td>
                        <td>{{ $user->scores->count() }}</td>
                        <td style="color:#c8c8d0;">{{ $user->created_at->format('d.m.Y') }}</td>
                        <td style="display:flex; gap:8px;">
                            @if ($user->id !== auth()->id())
                                {{-- Сделать админом / вернуть в участники --}}
                                <form method="POST" action="{{ route('admin.users.toggle-role', $user) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-small">
                                        {{ $user->isAdmin() ? 'Снять админа' : 'Сделать админом' }}
                                    </button>
                                </form>
                                {{-- Удалить пользователя --}}
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Удалить пользователя {{ $user->name }} и все его рекорды?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-small">Удалить</button>
                                </form>
                            @else
                                <span style="color:#c8c8d0; font-size:13px;">это вы</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
