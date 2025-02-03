<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use Database\Seeders\ItemSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\CategorySeeder;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ItemSeeder::class);
    }

    /**
     * 商品詳細ページで必要な情報が表示される
     */
    public function test_item_detail_page_displays_correct_information()
    {
        $item = Item::firstOrFail();

        Comment::create([
            'item_id' => $item->id,
            'user_id' => User::first()->id,
            'content' => 'これはテストコメントです。',
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertSee($item->name);
        $response->assertSee(number_format($item->price));
        $response->assertSee($item->condition);
        $response->assertSee($item->description);

        $response->assertSee($item->like_count);
        $response->assertSee($item->comments_count);

        $response->assertSee('これはテストコメントです。');
    }

    /**
     * 複数選択されたカテゴリが表示される
     */
    public function test_multiple_categories_are_displayed()
    {
        $item = Item::firstOrFail();

        $categories = Category::take(2)->get();
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get("/item/{$item->id}");

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
