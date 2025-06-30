<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('ログインユーザーはダッシュボードを表示できる', function () {
    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('ダッシュボード');
});

test('ダッシュボードメッセージが表示される', function () {
    $message = __('messages.dashboard_message');

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSee($message);
});

test('タスク一覧と新規作成へのリンクが表示される', function () {
    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSee('タスク一覧へ');
    $response->assertSee('新しいタスクを追加');
    $response->assertSee(route('tasks.index'), false);  // HTML属性値もチェック
    $response->assertSee(route('tasks.create'), false);
});
