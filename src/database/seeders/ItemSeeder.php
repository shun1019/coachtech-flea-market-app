<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $user1 = User::where('email', 'user1@example.com')->first();
        $user2 = User::where('email', 'user2@example.com')->first();

        if (!$user1 || !$user2) {
            return;
        }

        $itemsUser1 = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'items/Armani+Mens+Clock.jpg',
                'condition' => '良好',
                'status' => 'available',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'items/HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
                'status' => 'available',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'items/iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
                'status' => 'available',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'items/Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
                'status' => 'available',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'items/Living+Room+Laptop.jpg',
                'condition' => '良好',
                'status' => 'available',
            ],
        ];

        foreach ($itemsUser1 as $item) {
            $item['user_id'] = $user1->id;
            Item::create($item);
        }

        $itemsUser2 = [
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'items/Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
                'status' => 'available',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'items/Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
                'status' => 'available',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'items/Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
                'status' => 'available',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => 'items/Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
                'status' => 'available',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => 'items/外出メイクアップセット.jpg',
                'condition' => '目立った傷や汚れなし',
                'status' => 'available',
            ],
        ];

        foreach ($itemsUser2 as $item) {
            $item['user_id'] = $user2->id;
            Item::create($item);
        }
    }
}
