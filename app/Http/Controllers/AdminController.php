<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Models\User;
use Illuminate\Http\Request;

// Админ-панель: доступна только роли "Администратор"
class AdminController extends Controller
{
    public function __construct()
    {
        // Проверка роли на каждый запрос к админке
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Доступ только для администратора');
        }
    }

    // Главная админки: статистика + список пользователей
    public function index()
    {
        $users = User::with('scores')->orderBy('name')->get();
        $stats = [
            'users'      => User::count(),
            'games'      => Score::count(),
            'best_score' => (int) Score::max('points'),
        ];

        return view('admin', ['users' => $users, 'stats' => $stats]);
    }

    // Сделать участника админом (или вернуть обратно в участники)
    public function toggleRole(User $user)
    {
        // Себя нельзя разжалить — иначе можно остаться без админа
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Нельзя менять свою роль');
        }

        $user->update([
            'role' => $user->isAdmin() ? 'Участник' : 'Администратор',
        ]);

        return back()->with('success', "Роль пользователя {$user->name} изменена");
    }

    // Удалить пользователя (вместе с его рекордами)
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Нельзя удалить самого себя');
        }

        $user->delete();

        return back()->with('success', "Пользователь {$user->name} удалён");
    }
}
