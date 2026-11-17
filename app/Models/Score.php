<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'points', 'best_tile'])]
class Score extends Model
{
    // Один рекорд = один завершённый раунд игры 2048
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
