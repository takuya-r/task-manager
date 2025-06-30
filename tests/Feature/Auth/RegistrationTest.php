<?php

use App\Models\User;
use Illuminate\Validation\Rules;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});

test('登録成功：/dashboard にリダイレクトされる', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123!', // Rules\Password::defaults() に準拠
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();
});

test('登録失敗：空の入力値でバリデーションエラーになる', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response->assertRedirect('/register');
    $response->assertSessionHasErrors(['name', 'email', 'password']);
    $this->assertGuest();
});

test('登録失敗：email が不正な形式', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Test',
        'email' => 'invalid-email',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect('/register');
    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
});

test('登録失敗：password が確認欄と一致しない', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Test',
        'email' => 'test2@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Mismatch123!',
    ]);

    $response->assertRedirect('/register');
    $response->assertSessionHasErrors(['password']);
    $this->assertGuest();
});

test('登録失敗：name が256文字でエラー', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => str_repeat('a', 256),
        'email' => 'longname@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect('/register');
    $response->assertSessionHasErrors(['name']);
    $this->assertGuest();
});

test('登録失敗：email が重複している', function () {
    User::factory()->create([
        'email' => 'duplicate@example.com',
    ]);

    $response = $this->from('/register')->post('/register', [
        'name' => 'Test',
        'email' => 'duplicate@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect('/register');
    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
});
