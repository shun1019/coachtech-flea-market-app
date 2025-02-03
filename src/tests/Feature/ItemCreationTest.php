<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Database\Seeders\CategorySeeder;

class ItemCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品出品テスト
     */
    public function test_user_can_create_item()
    {
        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');

        $image = UploadedFile::fake()->create('test-item.jpg', 500, 'image/jpeg');

        $category = Category::firstOrFail();

        $response = $this->post('/sell', [
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品です。',
            'price' => 1000,
            'condition' => '新品',
            'categories' => [$category->id],
            'image' => $image,
        ]);

        $response->assertRedirect('/mypage');

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品です。',
            'price' => 1000,
            'condition' => '新品',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('category_items', [
            'category_id' => $category->id,
            'item_id' => Item::first()->id,
        ]);

        Storage::disk('public')->assertExists("items/{$image->hashName()}");
    }
}
