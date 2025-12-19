<?php

namespace Tests\Feature\Profile;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_mypage_shows_user_and_lists(): void
    {
        $user = User::factory()->create(['name' => 'MY_NAME']);

        // 出品
        $selling = Item::factory()->create(['seller_id' => $user->id, 'name' => 'SELLING_ITEM']);

        // 購入
        $boughtItem = Item::factory()->create(['name' => 'BOUGHT_ITEM']);
        Purchase::factory()->create(['buyer_id' => $user->id, 'item_id' => $boughtItem->id]);

        $res = $this->actingAs($user)->get('/mypage');

        $res->assertOk();
        $res->assertSee('MY_NAME');

        // どこかに表示されていればOK
        $res->assertSee('SELLING_ITEM');
        $res->assertSee('BOUGHT_ITEM');
    }

    public function test_profile_edit_shows_initial_values_and_update_saves(): void
    {
        $user = User::factory()->create([
            'name' => 'OLD_NAME',
            'postal_code' => '1111111',
            'address' => 'OLD_ADDRESS',
            'building' => 'OLD_BUILDING',
        ]);

        $res = $this->actingAs($user)->get('/mypage/profile');

        $res->assertOk();
        $res->assertSee('OLD_NAME');
        $res->assertSee('1111111');
        $res->assertSee('OLD_ADDRESS');

        $res = $this->actingAs($user)->post('/mypage/profile', [
            'name' => 'NEW_NAME',
            'postal_code' => '2222222',
            'address' => 'NEW_ADDRESS',
            'building' => 'NEW_BUILDING',
        ]);

        $res->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'NEW_NAME',
            'postal_code' => '2222222',
            'address' => 'NEW_ADDRESS',
            'building' => 'NEW_BUILDING',
        ]);
    }
}
