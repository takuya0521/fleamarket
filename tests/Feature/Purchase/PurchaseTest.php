<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_page_shows_item_and_address(): void
    {
        $user = User::factory()->create([
            'postal_code' => '1234567',
            'address' => 'TOKYO',
            'building' => 'BLD',
        ]);
        $item = Item::factory()->create(['name' => 'BUY_ITEM']);

        $res = $this->actingAs($user)->get("/purchase/{$item->id}");

        $res->assertOk();
        $res->assertSee('BUY_ITEM');
        $res->assertSee('1234567');
        $res->assertSee('TOKYO');
    }

    public function test_update_shipping_address_reflects_on_purchase(): void
    {
        $user = User::factory()->create([
            'postal_code' => '0000000',
            'address' => 'OLD',
            'building' => 'OLD_B',
        ]);
        $item = Item::factory()->create();

        $res = $this->actingAs($user)->post("/purchase/address/{$item->id}", [
            'postal_code' => '9999999',
            'address' => 'NEW_ADDR',
            'building' => 'NEW_BLD',
        ]);

        $res->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'postal_code' => '9999999',
            'address' => 'NEW_ADDR',
            'building' => 'NEW_BLD',
        ]);

        $res = $this->actingAs($user)->get("/purchase/{$item->id}");
        $res->assertOk();
        $res->assertSee('9999999');
        $res->assertSee('NEW_ADDR');
    }

    public function test_purchase_store_creates_purchase_or_redirects_to_stripe(): void
    {
        $buyer = User::factory()->create([
            'postal_code' => '1234567',
            'address' => 'TOKYO',
        ]);
        $item = Item::factory()->create();

        $res = $this->actingAs($buyer)->post("/purchase/{$item->id}", [
            'payment_method' => 'カード',
            'shipping_postal_code' => '1234567',
            'shipping_address' => 'TOKYO',
            'shipping_building' => 'BLD',
        ]);

        $res->assertStatus(302);

        // purchases が作られる実装ならこれが通る
        $created = Purchase::where('item_id', $item->id)->where('buyer_id', $buyer->id)->exists();

        // Stripeへ飛ぶ実装なら Location に stripe が含まれる想定
        $location = $res->headers->get('Location') ?? '';
        $redirectToStripe = stripos($location, 'stripe') !== false;

        $this->assertTrue($created || $redirectToStripe, "Purchase not created and not redirected to stripe. Location={$location}");

        // 作られているなら payment_method も確認（ID11の一部）
        if ($created) {
            $this->assertDatabaseHas('purchases', [
                'item_id' => $item->id,
                'buyer_id' => $buyer->id,
                'payment_method' => 'カード',
            ]);
        }
    }

    public function test_sold_label_after_purchase_record_exists(): void
    {
        $item = Item::factory()->create();
        Purchase::factory()->create(['item_id' => $item->id]);

        $res = $this->get('/');

        $res->assertOk();
        $this->assertTrue(stripos($res->getContent(), 'sold') !== false);
    }
}
