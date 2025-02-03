<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\ItemSeeder;
use Database\Seeders\UserSeeder;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);
    }

    /**
     * 商品名で部分一致検索ができる
     */
    public function test_search_items_by_name()
    {
        $this->assertDatabaseHas('items', ['name' => 'ノートPC']);

        $response = $this->get('/?search=PC');

        $response->assertSee('ノートPC');

        $response->assertDontSee('HDD');
        $response->assertDontSee('玉ねぎ3束');
    }

    /**
     * 検索状態がマイリストでも保持されている
     */
    public function test_search_state_is_kept_in_mylist()
    {
        $user = User::factory()->create();

        $likedItem = Item::where('name', 'ノートPC')->firstOrFail();

        $user->likes()->attach($likedItem->id);

        $this->actingAs($user);

        $response = $this->get('/?search=PC');

        $response->assertSee('ノートPC');

        $response = $this->get('/?tab=mylist&search=PC');

        $response->assertSee('ノートPC');

        $response->assertSee('value="PC"', false);
    }
}
