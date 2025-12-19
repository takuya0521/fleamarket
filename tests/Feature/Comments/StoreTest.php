<?php

namespace Tests\Feature\Comments;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_post_comment(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $res = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'body' => 'TEST_COMMENT',
        ]);

        $res->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'TEST_COMMENT',
        ]);
    }

    public function test_guest_cannot_post_comment(): void
    {
        $item = Item::factory()->create();

        $res = $this->post("/item/{$item->id}/comment", ['body' => 'TEST']);

        $res->assertStatus(302); // loginへ飛ぶ想定
    }

    public function test_comment_required(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $res = $this->actingAs($user)->post("/item/{$item->id}/comment", ['body' => '']);

        $res->assertSessionHasErrors(['body']);
    }

    public function test_comment_max_255(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $res = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'body' => str_repeat('a', 256),
        ]);

        $res->assertSessionHasErrors(['body']);
    }
}
