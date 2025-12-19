<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_validation_errors(): void
    {
        // name なし
        $res = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $res->assertSessionHasErrors(['name']);

        // email なし
        $res = $this->post('/register', [
            'name' => 'Taro',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $res->assertSessionHasErrors(['email']);

        // password なし
        $res = $this->post('/register', [
            'name' => 'Taro',
            'email' => 'test2@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);
        $res->assertSessionHasErrors(['password']);

        // 7文字以下
        $res = $this->post('/register', [
            'name' => 'Taro',
            'email' => 'test3@example.com',
            'password' => 'pass123', // 7以下
            'password_confirmation' => 'pass123',
        ]);
        $res->assertSessionHasErrors(['password']);

        // confirmation 不一致（キーが password or password_confirmation のどっちでも許容）
        $res = $this->post('/register', [
            'name' => 'Taro',
            'email' => 'test4@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password999',
        ]);
        $res->assertSessionHas('errors');
        $errors = session('errors');
        $this->assertTrue(
            $errors->has('password') || $errors->has('password_confirmation'),
            'Expected validation error on password or password_confirmation.'
        );
    }

    public function test_register_success_redirects_to_verify_notice_or_mypage(): void
    {
        $res = $this->post('/register', [
            'name' => 'Taro',
            'email' => 'ok@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $res->assertStatus(302);

        $location = $res->headers->get('Location') ?? '';
        $path = parse_url($location, PHP_URL_PATH) ?? '';

        $allowed = ['/verify/notice', '/mypage/profile', '/mypage'];

        $this->assertTrue(
            in_array($path, $allowed, true),
            "Unexpected redirect location: {$location}"
        );
    }
}
