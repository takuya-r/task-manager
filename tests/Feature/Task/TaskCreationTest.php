<?php

use Illuminate\Support\Str;
use function Pest\Laravel\post;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    $this->actingAs($this->user);
});

test('title 正常系', function () {
    $response1 = post('/tasks', [
        'title' => '買い物メモ',
        'content' => '内容',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ]);
    $response1->assertSessionHasNoErrors();
    $response1->assertRedirect('/tasks');

    assertDatabaseHas('tasks', [
        'title' => '買い物メモ',
        'user_id' => $this->user->id,
    ]);

    $response2 = post('/tasks', [
        'title' => '😀タスク',
        'content' => '内容',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ]);
    $response2->assertSessionHasNoErrors();
    $response2->assertRedirect('/tasks');

    assertDatabaseHas('tasks', [
        'title' => '😀タスク',
        'user_id' => $this->user->id,
    ]);
});

test('title 異常系', function () {
    post('/tasks', [
        'title' => '',
        'content' => '内容',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ])->assertSessionHasErrors(['title' => 'タイトルは必須項目です。']);

    post('/tasks', [
        'title' => Str::random(51),
        'content' => '内容',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ])->assertSessionHasErrors(['title' => 'タイトルの文字数は、50文字以下である必要があります。']);
});

test('content 正常系', function () {
    $response1 = post('/tasks', [
        'title' => 'テスト1',
        'content' => '飲み物を買う',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ]);
    $response1->assertSessionHasNoErrors();
    $response1->assertRedirect('/tasks');

    assertDatabaseHas('tasks', [
        'title' => 'テスト1',
        'content' => '飲み物を買う',
        'user_id' => $this->user->id,
    ]);

    $response2 = post('/tasks', [
        'title' => 'テスト2',
        'content' => '🍜を買う',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ]);
    $response2->assertSessionHasNoErrors();
    $response2->assertRedirect('/tasks');

    $response3 = post('/tasks', [
        'title' => 'テスト3',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ]);
    $response3->assertSessionHasNoErrors();
    $response3->assertRedirect('/tasks');
});

test('content 異常系', function () {
    post('/tasks', [
        'title' => 'テスト',
        'content' => Str::random(501),
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ])->assertSessionHasErrors(['content' => '内容の文字数は、500文字以下である必要があります。']);
});

test('due_date 正常系', function () {
    $response = post('/tasks', [
        'title' => 'テスト',
        'content' => '',
        'due_date' => '2025-12-31 12:30',
    ]);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/tasks');

    assertDatabaseHas('tasks', [
        'title' => 'テスト',
        'due_date' => '2025-12-31 12:30',
        'user_id' => $this->user->id,
    ]);
});

test('due_date 異常系', function () {
    post('/tasks', [
        'title' => 'テスト',
        'content' => '',
        'due_date' => '😄',
    ])->assertSessionHasErrors(['due_date' => '締切日は、正しい日付ではありません。']);

    post('/tasks', [
        'title' => 'テスト',
        'content' => '',
        'due_date' => '',
    ])->assertSessionHasErrors(['due_date' => '締切日は必須項目です。']);
});

test('tags 正常系', function () {
    $response1 = post('/tasks', [
        'title' => 'タグ付き1',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '買い出し,食事',
    ]);
    $response1->assertSessionHasNoErrors();
    $response1->assertRedirect('/tasks');

    $response2 = post('/tasks', [
        'title' => 'タグ付き2',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '🍜,☕',
    ]);
    $response2->assertSessionHasNoErrors();
    $response2->assertRedirect('/tasks');

    $response3 = post('/tasks', [
        'title' => 'タグなし',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '',
    ]);
    $response3->assertSessionHasNoErrors();
    $response3->assertRedirect('/tasks');
});

test('tags 異常系', function () {
    $longTag = Str::random(51);

    post('/tasks', [
        'title' => 'タグ長すぎ',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => $longTag,
    ])->assertSessionHasErrors(['tags' => 'タグの文字数は、50文字以下である必要があります。']);
});
