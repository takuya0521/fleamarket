<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $res->assertStatus(302);
    }
}
