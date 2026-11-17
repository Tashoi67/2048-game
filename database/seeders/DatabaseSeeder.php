<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Наполняем базу стартовыми данными.
     */
    public function run(): void
    {
        // Аккаунт администратора по умолчанию
        User::firstOrCreate([
            'name' => 'admin',
        ], [
            'password' => 'admin123',
            'role'     => 'Администратор',
        ]);
    }
}
