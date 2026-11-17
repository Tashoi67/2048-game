<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

// Публичные страницы — видны всем
Route::get('/', [GameController::class, 'home'])->name('home');
Route::get('/leaderboard', [GameController::class, 'leaderboard'])->name('leaderboard');

// Авторизация
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Игра и сохранение результата — только для вошедших
Route::middleware('auth')->group(function () {
    Route::get('/game', [GameController::class, 'game'])->name('game');
    Route::post('/score', [GameController::class, 'submitScore'])->name('score.submit');
});

// Админ-панель — доступ внутри контроллера проверяет роль
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin');
    Route::post('/users/{user}/toggle-role', [AdminController::class, 'toggleRole'])->name('admin.users.toggle-role');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
});
