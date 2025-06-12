<?php

use App\Models\User;
use App\Models\Task;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

test('トップページ：未ログイン時に welcome-custom bladeが表示される', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('welcome_custom');
});

test('未ログイン状態で保護されたページにアクセスすると /login にリダイレクトされる', function () {
    $protectedUris = [
        '/dashboard',
        '/tasks',
        '/profile',
        '/tasks/create',
        '/tasks/1/edit', // 存在しないIDでも認可前なら401や302になる
    ];

    foreach ($protectedUris as $uri) {
        $response = get($uri);
        $actual = $response->status();
        $expected = 302; // assertRedirect は内部的に status 302 を確認している

        expect($actual === $expected)
            ->toBeTrue("【エラー】URI「{$uri}」は未ログイン時にログイン画面へリダイレクトされる必要があります（期待: {$expected}、実際: {$actual}）");
    }
});

test('トップページ：ログイン時は /dashboard にリダイレクトされる', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect('/dashboard');
});

test('ログイン時にその他のページへ正常アクセスできる', function () {
    $user = User::factory()->create();
    actingAs($user);

    // テスト用タスクを用意
    Task::factory()->count(1)->for($user)->create();

    $pages = [
        ['/dashboard', 200],
        ['/tasks', 200],
        ['/tasks/create', 200],
        ['/profile', 200],
        ['/tasks/1/edit', 200],
    ];

    foreach ($pages as [$uri, $expectedStatus]) {
        $response = get($uri);
        $actualStatus = $response->status();

        expect($actualStatus === $expectedStatus)
            ->toBeTrue("【エラー】URI「{$uri}」のステータスコードが期待値（{$expectedStatus}）と一致しません。実際のステータス: {$actualStatus}");
    }
});
