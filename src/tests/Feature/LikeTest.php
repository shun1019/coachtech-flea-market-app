<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\ItemSeeder;
use Database\Seeders\UserSeeder;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);
    }

    /**
     * いいねできる
     */
    public function test_user_can_like_an_item()
    {
        $user = User::firstOrFail();
        $item = Item::firstOrFail();

        $this->actingAs($user);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->post("/item/{$item->id}/like");

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $item->refresh();
        $this->assertEquals(1, $item->like_count);
    }

    /**
     * いいねアイコンがアクティブ状態に変化する
     */
    public function test_like_icon_changes_after_liking()
    {
        $user = User::firstOrFail();
        $item = Item::firstOrFail();

        $this->actingAs($user);

        $this->post("/item/{$item->id}/like");

        $response = $this->get("/item/{$item->id}");

        $response->assertSee('like-icon liked');
    }

    /**
     * いいねを解除できる
     */
    public function test_user_can_unlike_an_item()
    {
        $user = User::firstOrFail();
        $item = Item::firstOrFail();

        $this->actingAs($user);

        $this->post("/item/{$item->id}/like");

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->post("/item/{$item->id}/like");

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $item->refresh();
        $this->assertEquals(0, $item->like_count);
    }
}
