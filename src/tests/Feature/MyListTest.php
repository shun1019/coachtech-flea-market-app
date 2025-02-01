<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいねした商品だけが表示される
     */
    public function test_only_liked_items_are_displayed()
    {
        $user = User::factory()->create();
        $likedItem = Item::factory()->create(['name' => 'いいねした商品']);
        $otherItem = Item::factory()->create(['name' => 'いいねしていない商品']);

        $user->likes()->attach($likedItem->id);

        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');

        $response->assertSee('いいねした商品');

        $response->assertDontSee('いいねしていない商品');
    }

    /**
     * 購入済み商品は「SOLD」と表示される
     */
    public function test_purchased_items_display_sold_label()
    {
        $user = User::factory()->create();
        $purchasedItem = Item::factory()->create([
            'name' => '購入済み商品',
            'status' => 'sold'
        ]);

        $user->likes()->attach($purchasedItem->id);

        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');

        $response->assertSee('SOLD');
    }

    /**
     * 自分が出品した商品は一覧に表示されない
     */
    public function test_user_items_are_not_displayed()
    {
        $user = User::factory()->create();
        $ownItem = Item::factory()->create(['user_id' => $user->id, 'name' => '自分の商品']);
        $otherItem = Item::factory()->create(['name' => '他人の商品']);

        $user->likes()->attach($otherItem->id);

        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');

        $response->assertDontSee('自分の商品');

        $response->assertSee('他人の商品');
    }

    /**
     * 未認証の場合は何も表示されない
     */
    public function test_guest_cannot_see_mylist()
    {
        $likedItem = Item::factory()->create(['name' => 'いいねした商品']);

        $response = $this->get('/?tab=mylist');

        $response->assertDontSee('いいねした商品');
    }
}
