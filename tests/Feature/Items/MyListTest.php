<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\Like;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    public function test_mylist_shows_only_liked_items(): void
    {
        $user = User::factory()->create();

        $liked = Item::factory()->create(['name' => 'LIKED_ITEM']);
        $notLiked = Item::factory()->create(['name' => 'NOT_LIKED_ITEM']);

        Like::factory()->create(['user_id' => $user->id, 'item_id' => $liked->id]);

        $res = $this->actingAs($user)->get('/?tab=mylist');

        $res->assertOk();
        $res->assertSee('LIKED_ITEM');
        $res->assertDontSee('NOT_LIKED_ITEM');
    }

    public function test_mylist_shows_sold_label_for_purchased_item(): void
    {
        $user = User::factory()->create();

        $item = Item::factory()->create(['name' => 'LIKED_SOLD_ITEM']);
        Like::factory()->create(['user_id' => $user->id, 'item_id' => $item->id]);
        Purchase::factory()->create(['item_id' => $item->id]);

        $res = $this->actingAs($user)->get('/?tab=mylist');

        $res->assertOk();
        $this->assertTrue(stripos($res->getContent(), 'sold') !== false);
    }

    public function test_guest_mylist_shows_nothing_or_redirects(): void
    {
        $item = Item::factory()->create(['name' => 'SHOULD_NOT_SHOW']);

        $res = $this->get('/?tab=mylist');

        // 実装によって「空表示(200)」か「ログインへ(302)」どちらでもOK
        $this->assertTrue(in_array($res->getStatusCode(), [200, 302], true));

        if ($res->getStatusCode() === 200) {
            $res->assertDontSee('SHOULD_NOT_SHOW');
        }
    }
}
