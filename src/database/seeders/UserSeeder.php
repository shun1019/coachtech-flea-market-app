<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) {
                // ✅ ユーザーごとにプロフィールを作成
                $user->profile()->create([
                    'zipcode' => '123-4567',
                    'address' => '東京都渋谷区テスト町1-1',
                    'building' => 'テストマンション101',
                ]);
            });
    }
}
