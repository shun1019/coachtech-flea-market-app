<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user1 = User::create([
            'username' => 'ユーザー1',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);
        $user1->profile()->create([
            'zipcode' => '123-0001',
            'address' => '東京都千代田区1-1-1',
            'building' => '〇〇ビル1F',
        ]);

        $user2 = User::create([
            'username' => 'ユーザー2',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);
        $user2->profile()->create([
            'zipcode' => '456-0002',
            'address' => '大阪府大阪市中央区2-2-2',
            'building' => '△△マンション202',
        ]);

        $user3 = User::create([
            'username' => 'ユーザー3',
            'email' => 'user3@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);
        $user3->profile()->create([
            'zipcode' => '789-0003',
            'address' => '福岡県福岡市博多区3-3-3',
            'building' => '□□アパート303',
        ]);
    }
}
