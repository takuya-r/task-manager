<?php

use App\Models\User;
use App\Models\Task;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\patch;
use function Pest\Laravel\delete;
use function Pest\Laravel\actingAs;

test('トップページ：未ログイン時に welcome-custom bladeが表示されること', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('welcome_custom');
});

test('未ログイン状態で保護されたページにアクセスすると /login にリダイレクトされること', function () {
    $protectedUris = [
        '/dashboard',
        '/tasks',
        '/profile',
        '/tasks/create',
        '/tasks/1/edit', // 存在しないIDでも認可前なら302になる
    ];

    foreach ($protectedUris as $uri) {
        $response = get($uri);
        $actualStatus = $response->status();
        $expectedStatus = 302;
        $location = $response->headers->get('Location');

        expect($actualStatus === $expectedStatus)
            ->toBeTrue("【エラー】URI「{$uri}」は未ログイン時にステータス {$expectedStatus} でリダイレクトされる必要があります（実際: {$actualStatus}）");

        expect($location === '/login' || str_contains($location, '/login'))
            ->toBeTrue("【エラー】URI「{$uri}」は /login にリダイレクトされる必要があります（実際のリダイレクト先: {$location}）");
    }
});

test('未ログイン状態でPOST系リクエストを送信すると /login にリダイレクトされること', function () {
    $protectedActions = [
        ['uri' => '/tasks', 'method' => 'post'],
        ['uri' => '/tasks/1', 'method' => 'put'],
        ['uri' => '/profile', 'method' => 'patch'],
        ['uri' => '/profile', 'method' => 'delete'],
    ];

    foreach ($protectedActions as $action) {
        $method = $action['method'];
        $uri = $action['uri'];

        $response = match ($method) {
            'post' => post($uri),
            'put' => put($uri),
            'patch' => patch($uri),
            'delete' => delete($uri),
            default => throw new Exception("未対応のHTTPメソッド: {$method}"),
        };

        $actualStatus = $response->status();
        $expectedStatus = 302;
        $location = $response->headers->get('Location');

        expect($actualStatus === $expectedStatus)
            ->toBeTrue("【エラー】{$method} {$uri} は未ログイン時にステータス {$expectedStatus} でリダイレクトされる必要があります（実際: {$actualStatus}）");

        expect($location === '/login' || str_contains($location, '/login'))
            ->toBeTrue("【エラー】{$method} {$uri} のリダイレクト先が /login ではありません（実際: {$location}）");
    }
});

test('トップページ：ログイン時は /dashboard にリダイレクトされること', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect('/dashboard');
});

test('ログイン時にその他のページへ正常アクセスできること', function () {
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
