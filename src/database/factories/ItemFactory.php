<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(1000, 50000),
            'condition' => $this->faker->randomElement(['new', 'used']),
            'image' => 'default.jpg',
            'status' => 'available',
            'like_count' => 0,
            'comments_count' => 0,
        ];
    }
}
