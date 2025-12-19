<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @return string 検索が効いたクエリキー */
    private function detectSearchKey(string $keyword): string
    {
        $keys = ['keyword', 'search', 'q', 'name'];

        foreach ($keys as $key) {
            $res = $this->get('/?' . $key . '=' . urlencode($keyword));
            if ($res->isOk() && str_contains($res->getContent(), $keyword)) {
                return $key;
            }
        }

        $this->fail('Search query parameter not matched. Update keys list in SearchTest.');
    }

    public function test_partial_match_search_on_index(): void
    {
        Item::factory()->create(['name' => 'APPLE_123']);
        Item::factory()->create(['name' => 'BANANA_999']);

        $key = $this->detectSearchKey('APPLE');

        $res = $this->get('/?' . $key . '=APPLE');

        $res->assertOk();
        $res->assertSee('APPLE_123');
        $res->assertDontSee('BANANA_999');
    }

    public function test_search_is_kept_on_mylist_tab(): void
    {
        $user = User::factory()->create();

        $apple = Item::factory()->create(['name' => 'APPLE_123']);
        $banana = Item::factory()->create(['name' => 'BANANA_999']);

        Like::factory()->create(['user_id' => $user->id, 'item_id' => $apple->id]);
        Like::factory()->create(['user_id' => $user->id, 'item_id' => $banana->id]);

        $key = $this->detectSearchKey('APPLE');

        $res = $this->actingAs($user)->get('/?tab=mylist&' . $key . '=APPLE');

        $res->assertOk();
        $res->assertSee('APPLE_123');
        $res->assertDontSee('BANANA_999');
    }
}
