<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 全商品が取得できる
     */
    public function test_all_items_are_displayed()
    {
        Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);

        foreach (Item::all() as $item) {
            $response->assertSee($item->name);
        }
    }

    /**
     * 購入済み商品は「Sold」と表示される
     */
    public function test_purchased_items_display_sold_label()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'status' => 'sold',
            'name' => '購入済み商品'
        ]);

        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertSee('購入済み商品');

        $response->assertSeeText('SOLD');
    }

    /**
     * 自分が出品した商品は一覧に表示されない
     */
    public function test_user_items_are_not_displayed()
    {
        $user = User::factory()->create();
        $ownItem = Item::factory()->create(['user_id' => $user->id, 'name' => '自分の商品']);
        $otherItem = Item::factory()->create(['name' => '他人の商品']);

        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertDontSee('自分の商品');

        $response->assertSee('他人の商品');
    }
}
