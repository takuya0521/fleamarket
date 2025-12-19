<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_items_for_guest(): void
    {
        Item::factory()->create(['name' => 'ITEM_A']);
        Item::factory()->create(['name' => 'ITEM_B']);

        $res = $this->get('/');

        $res->assertOk();
        $res->assertSee('ITEM_A');
        $res->assertSee('ITEM_B');
    }

    public function test_index_hides_my_own_items_when_logged_in(): void
    {
        $me = User::factory()->create();
        Item::factory()->create(['seller_id' => $me->id, 'name' => 'MY_ITEM']);
        Item::factory()->create(['name' => 'OTHER_ITEM']);

        $res = $this->actingAs($me)->get('/');

        $res->assertOk();
        $res->assertDontSee('MY_ITEM');
        $res->assertSee('OTHER_ITEM');
    }

    public function test_index_shows_sold_label_for_purchased_item(): void
    {
        $item = Item::factory()->create(['name' => 'SOLD_ITEM']);
        Purchase::factory()->create(['item_id' => $item->id]);

        $res = $this->get('/');

        $res->assertOk();
        $this->assertTrue(
            stripos($res->getContent(), 'sold') !== false,
            'Sold label not found in HTML.'
        );
    }
}
