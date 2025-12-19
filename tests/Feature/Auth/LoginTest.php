<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_validation_errors(): void
    {
        $res = $this->post('/login', ['email' => '', 'password' => 'password123']);
        $res->assertSessionHasErrors(['email']);

        $res = $this->post('/login', ['email' => 'test@example.com', 'password' => '']);
        $res->assertSessionHasErrors(['password']);
    }

    public function test_login_fails_with_wrong_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $res = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpass',
        ]);

        $this->assertGuest();
        $res->assertSessionHasErrors(); // Fortify/Breeze系はここに入る
    }

    public function test_login_success(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $res = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $res->assertStatus(302);
    }
}
