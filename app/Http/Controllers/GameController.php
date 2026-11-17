<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;

// Страницы: главная, игра 2048, таблица рекордов, сохранение результата
class GameController extends Controller
{
    // Главная страница
    public function home()
    {
        // Топ-5 рекордов для главной страницы
        $topScores = Score::with('user')->orderByDesc('points')->limit(5)->get();

        return view('home', ['topScores' => $topScores]);
    }

    // Страница с игрой (только для вошедших)
    public function game()
    {
        return view('game', ['bestScore' => auth()->user()->bestScore()]);
    }

    // Сохранить результат раунда (сюда стучится JavaScript после игры)
    public function submitScore(Request $request)
    {
        $data = $request->validate([
            'points'    => 'required|integer|min:0|max:1000000',
            'best_tile' => 'required|integer|min:2|max:131072',
        ]);

        Score::create([
            'user_id'   => auth()->id(),
            'points'    => $data['points'],
            'best_tile' => $data['best_tile'],
        ]);

        return response()->json(['status' => 'ok', 'best' => auth()->user()->bestScore()]);
    }

    // Таблица рекордов
    public function leaderboard()
    {
        $scores = Score::with('user')->orderByDesc('points')->limit(15)->get();

        return view('leaderboard', ['scores' => $scores]);
    }
}
