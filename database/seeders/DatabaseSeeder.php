<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Giriş yapıp korumalı endpoint'leri test edebilmen için bir kullanıcı.
        // Şifre 'password' olarak kaydedilir (User modelindeki 'hashed' cast'i otomatik hash'ler).
        User::updateOrCreate(
            ['mail' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'status' => 'active',
            ]
        );
    }
}
