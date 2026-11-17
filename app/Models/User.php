<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed', // пароль автоматически хешируется
        ];
    }

    // Все рекорды пользователя
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    // Лучший рекорд пользователя (или 0, если ещё не играл)
    public function bestScore(): int
    {
        return (int) $this->scores()->max('points');
    }

    // Проверки роли
    public function isAdmin(): bool
    {
        return $this->role === 'Администратор';
    }
}
