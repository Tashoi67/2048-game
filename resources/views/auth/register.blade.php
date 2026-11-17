@extends('partials.layout')

@section('title', 'Регистрация — 2048 Game')

@section('content')
    <div class="card auth-card">
        <h1>Регистрация</h1>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <p style="margin:4px 0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label>Логин</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus minlength="3">
            </div>

            <div class="form-group">
                <label>Пароль (минимум 6 символов)</label>
                <input type="password" name="password" required minlength="6">
            </div>

            <div class="form-group">
                <label>Повторите пароль</label>
                <input type="password" name="password_confirmation" required minlength="6">
            </div>

            <button type="submit" class="btn btn-primary">Создать аккаунт</button>
        </form>

        <p class="switch-link">Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a></p>
    </div>
@endsection
