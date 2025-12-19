<?php

namespace Tests\Feature\Items;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_detail_shows_required_information_and_categories_and_comments(): void
    {
        $seller = User::factory()->create(['name' => 'SELLER']);
        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'ITEM_NAME',
            'brand' => 'ITEM_BRAND',
            'description' => 'ITEM_DESC',
        ]);

        $catA = Category::factory()->create(['name' => 'CAT_A']);
        $catB = Category::factory()->create(['name' => 'CAT_B']);

        // categories() がある前提。無い場合は中間テーブル insert に切り替えてください。
        if (method_exists($item, 'categories')) {
            $item->categories()->attach([$catA->id, $catB->id]);
        }

        $commenter = User::factory()->create(['name' => 'COMMENTER']);
        Comment::factory()->create([
            'user_id' => $commenter->id,
            'item_id' => $item->id,
            'body' => 'HELLO_COMMENT',
        ]);

        $res = $this->get("/item/{$item->id}");

        $res->assertOk();
        $res->assertSee('ITEM_NAME');
        $res->assertSee('ITEM_BRAND');
        $res->assertSee('ITEM_DESC');
        $res->assertSee('HELLO_COMMENT');
        $res->assertSee('CAT_A');
        $res->assertSee('CAT_B');
    }
}
