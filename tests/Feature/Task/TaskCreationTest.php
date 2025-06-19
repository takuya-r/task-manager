<?php

use Illuminate\Support\Str;
use function Pest\Laravel\post;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    $this->actingAs($this->user);
});

test('title 正常系', function () {
    post('/tasks', [
        'title' => '買い物メモ',
        'content' => '内容',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'title' => '買い物メモ',
        'user_id' => $this->user->id,
    ]);

    post('/tasks', [
        'title' => '😀タスク',
        'content' => '内容',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ])->assertSessionHasNoErrors();

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
    post('/tasks', [
        'title' => 'テスト1',
        'content' => '飲み物を買う',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'title' => 'テスト1',
        'content' => '飲み物を買う',
        'user_id' => $this->user->id,
    ]);

    post('/tasks', [
        'title' => 'テスト2',
        'content' => '🍜を買う',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ])->assertSessionHasNoErrors();

    post('/tasks', [
        'title' => 'テスト3',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ])->assertSessionHasNoErrors();
});

test('content 異常系', function () {
    post('/tasks', [
        'title' => 'テスト',
        'content' => Str::random(501),
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
    ])->assertSessionHasErrors(['content' => '内容の文字数は、500文字以下である必要があります。']);
});

test('due_date 正常系', function () {
    post('/tasks', [
        'title' => 'テスト',
        'content' => '',
        'due_date' => '2025-12-31 12:30',
    ])->assertSessionHasNoErrors();

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
    post('/tasks', [
        'title' => 'タグ付き1',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '買い出し,食事',
    ])->assertSessionHasNoErrors();

    post('/tasks', [
        'title' => 'タグ付き2',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '🍜,☕',
    ])->assertSessionHasNoErrors();

    post('/tasks', [
        'title' => 'タグなし',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '',
    ])->assertSessionHasNoErrors();
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
