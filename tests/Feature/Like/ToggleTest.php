<?php

namespace Tests\Feature\Like;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_like_toggle_add_and_remove(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $url = "/item/{$item->id}/like";

        $this->actingAs($user)->post($url)->assertStatus(302);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user)->post($url)->assertStatus(302);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
