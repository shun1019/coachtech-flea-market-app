<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー情報編集ページに過去の設定が反映されていることを確認
     */
    public function test_profile_edit_page_displays_existing_user_data()
    {
        $user = User::factory()->create();

        $profile = \App\Models\UserProfile::create([
            'user_id' => $user->id,
            'profile_image' => 'profile_images/sample.jpg',
            'zipcode' => '123-4567',
            'address' => '東京都渋谷区テスト町1-1',
            'building' => 'テストマンション101',
        ]);

        $response = $this->actingAs($user)->get('/mypage/profile/edit');

        $response->assertSee($user->name)
            ->assertSee('123-4567')
            ->assertSee('東京都渋谷区テスト町1-1')
            ->assertSee('テストマンション101')
            ->assertSee('profile_images/sample.jpg');
    }
}
