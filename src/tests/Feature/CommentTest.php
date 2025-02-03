<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\UserSeeder;
use Database\Seeders\ItemSeeder;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(ItemSeeder::class);
    }

    /**
     * ログイン済みのユーザーはコメントを送信できる
     */
    public function test_authenticated_user_can_post_comment()
    {
        $user = User::firstOrFail();
        $item = Item::firstOrFail();

        $this->actingAs($user);

        $response = $this->post("/item/{$item->id}/comment", [
            'content' => 'これはテストコメントです。',
        ]);

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => 'これはテストコメントです。',
        ]);

        $item->refresh();
        $this->assertEquals(1, $item->comments()->count());
    }

    /**
     * 未ログインユーザーはコメントを送信できない
     */
    public function test_guest_cannot_post_comment()
    {
        $item = Item::firstOrFail();

        $response = $this->post("/item/{$item->id}/comment", [
            'content' => 'これはテストコメントです。',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'これはテストコメントです。',
        ]);
    }

    /**
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_comment_requires_content()
    {
        $user = User::firstOrFail();
        $item = Item::firstOrFail();

        $this->actingAs($user);

        $response = $this->post("/item/{$item->id}/comment", [
            'content' => '',
        ]);

        $response->assertSessionHasErrors([
            'content' => 'コメントを入力してください。',
        ]);
    }

    /**
     * コメントが255文字を超える場合、バリデーションエラーが発生する
     */
    public function test_comment_cannot_exceed_255_characters()
    {
        $user = User::firstOrFail();
        $item = Item::firstOrFail();

        $this->actingAs($user);

        $longComment = str_repeat('あ', 256);

        $response = $this->post("/item/{$item->id}/comment", [
            'content' => $longComment,
        ]);

        $response->assertSessionHasErrors([
            'content' => 'コメントは255文字以内で入力してください。',
        ]);
    }
}
