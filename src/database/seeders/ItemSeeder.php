<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'name' => 'アイテム1',
                'price' => 1000,
                'description' => '説明文1',
                'condition' => '新品',
                'image' => 'image1.png',
                'user_id' => 1, // ユーザーIDを指定
            ],
            [
                'name' => 'アイテム2',
                'price' => 2000,
                'description' => '説明文2',
                'condition' => '中古',
                'image' => 'image2.png',
                'user_id' => 1, // ユーザーIDを指定
            ],
        ];

        foreach ($items as $itemData) {
            $item = Item::create($itemData);
            $categories = Category::inRandomOrder()->take(3)->pluck('id');
            $item->categories()->attach($categories);
        }
    }
}
