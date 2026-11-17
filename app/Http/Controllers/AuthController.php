<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Регистрация, вход и выход — так же, как на новостном сайте
class AuthController extends Controller
{
    // Показать форму регистрации
    public function showRegister()
    {
        return view('auth.register');
    }

    // Обработать регистрацию
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|min:3|max:50|unique:users,name',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required'      => 'Введите логин',
            'name.unique'        => 'Такой логин уже занят',
            'name.min'           => 'Логин минимум 3 символа',
            'password.required'  => 'Введите пароль',
            'password.min'       => 'Пароль минимум 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ]);

        // Новый пользователь всегда получает роль "Участник"
        $user = User::create([
            'name'     => $data['name'],
            'password' => $data['password'],
            'role'     => 'Участник',
        ]);

        // Автоматически входим после регистрации
        Auth::login($user);

        return redirect()->route('game');
    }

    // Показать форму входа
    public function showLogin()
    {
        return view('auth.login');
    }

    // Обработать вход
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name'     => 'required|string',
            'password' => 'required|string',
        ], [
            'name.required'     => 'Введите логин',
            'password.required' => 'Введите пароль',
        ]);

        // Пытаемся войти
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('game');
        }

        // Если не получилось — возвращаем с ошибкой
        return back()->withErrors([
            'name' => 'Неверный логин или пароль',
        ])->onlyInput('name');
    }

    // Выход
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
