<?php

use Illuminate\Support\Str;
use function Pest\Laravel\put;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    $this->task = \App\Models\Task::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
});

test('title 正常系', function () {
    $dueDate = now()->addDay()->format('Y-m-d H:i:00');

    put("/tasks/{$this->task->id}", [
        'title' => '買い物メモ',
        'content' => '内容',
        'due_date' => $dueDate,
        'tags' => '日用品',
        'status' => '未着手',
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'title' => '買い物メモ',
        'content' => '内容',
        'due_date' => $dueDate,
        'status' => '未着手',
    ]);

    assertDatabaseHas('tags', [
        'name' => '日用品',
    ]);

    put("/tasks/{$this->task->id}", [
        'title' => '😀タスク',
        'content' => '内容',
        'due_date' => $dueDate,
        'tags' => '絵文字',
        'status' => '未着手',
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'title' => '😀タスク',
        'content' => '内容',
        'due_date' => $dueDate,
        'status' => '未着手',
    ]);

    assertDatabaseHas('tags', [
        'name' => '絵文字',
    ]);
});

test('title 異常系', function () {
    put("/tasks/{$this->task->id}", [
        'title' => '',
        'content' => '内容',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '',
        'status' => '未着手',
    ])->assertSessionHasErrors(['title' => 'タイトルは必須項目です。']);

    put("/tasks/{$this->task->id}", [
        'title' => Str::random(51),
        'content' => '内容',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '',
        'status' => '未着手',
    ])->assertSessionHasErrors(['title' => 'タイトルの文字数は、50文字以下である必要があります。']);
});

test('content 正常系', function () {
    $dueDate = now()->addDay()->format('Y-m-d H:i:00');

    put("/tasks/{$this->task->id}", [
        'title' => 'テスト',
        'content' => '飲み物を買う',
        'due_date' => $dueDate,
        'tags' => '',
        'status' => '未着手',
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'title' => 'テスト',
        'content' => '飲み物を買う',
        'due_date' => $dueDate,
        'status' => '未着手',
    ]);

    put("/tasks/{$this->task->id}", [
        'title' => 'テスト',
        'content' => '🍜を買う',
        'due_date' => $dueDate,
        'tags' => '',
        'status' => '未着手',
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'title' => 'テスト',
        'content' => '🍜を買う',
        'due_date' => $dueDate,
        'status' => '未着手',
    ]);

    put("/tasks/{$this->task->id}", [
        'title' => 'テスト',
        'content' => '',
        'due_date' => $dueDate,
        'tags' => '',
        'status' => '未着手',
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'title' => 'テスト',
        'content' => null,
        'due_date' => $dueDate,
        'status' => '未着手',
    ]);
});

test('content 異常系', function () {
    put("/tasks/{$this->task->id}", [
        'title' => 'テスト',
        'content' => Str::random(501),
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '',
        'status' => '未着手',
    ])->assertSessionHasErrors(['content' => '内容の文字数は、500文字以下である必要があります。']);
});

test('due_date 正常系', function () {
    put("/tasks/{$this->task->id}", [
        'title' => 'テスト',
        'content' => '',
        'due_date' => '2025-12-31 12:30',
        'tags' => '',
        'status' => '未着手',
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'title' => 'テスト',
        'content' => null,
        'due_date' => '2025-12-31 12:30',
        'status' => '未着手',
    ]);
});

test('due_date 異常系', function () {
    put("/tasks/{$this->task->id}", [
        'title' => 'テスト',
        'content' => '',
        'due_date' => '😄',
        'tags' => '',
        'status' => '未着手',
    ])->assertSessionHasErrors(['due_date' => '締切日は、正しい日付ではありません。']);

    put("/tasks/{$this->task->id}", [
        'title' => 'テスト',
        'content' => '',
        'due_date' => '',
        'tags' => '',
        'status' => '未着手',
    ])->assertSessionHasErrors(['due_date' => '締切日は必須項目です。']);
});

test('tags 正常系', function () {
    $dueDate = now()->addDay()->format('Y-m-d H:i:00');

    put("/tasks/{$this->task->id}", [
        'title' => 'タグあり',
        'content' => '',
        'due_date' => $dueDate,
        'tags' => '買い出し,食事',
        'status' => '未着手',
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'title' => 'タグあり',
        'content' => null,
        'due_date' => $dueDate,
        'status' => '未着手',
    ]);

    assertDatabaseHas('tags', [
        'name' => '買い出し',
        'name' => '食事',
    ]);

    put("/tasks/{$this->task->id}", [
        'title' => 'タグ絵文字',
        'content' => '',
        'due_date' => $dueDate,
        'tags' => '🍜,☕',
        'status' => '未着手',
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'title' => 'タグ絵文字',
        'content' => null,
        'due_date' => $dueDate,
        'status' => '未着手',
    ]);

    assertDatabaseHas('tags', [
        'name' => '🍜',
        'name' => '☕',
    ]);

    put("/tasks/{$this->task->id}", [
        'title' => 'タグ空',
        'content' => '',
        'due_date' => $dueDate,
        'tags' => '',
        'status' => '未着手',
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'title' => 'タグ空',
        'content' => null,
        'due_date' => $dueDate,
        'status' => '未着手',
    ]);

    assertDatabaseMissing('tag_task', [
        'task_id' => $this->task->id,
    ]);
});

test('tags 異常系', function () {
    put("/tasks/{$this->task->id}", [
        'title' => 'タグ長すぎ',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => Str::random(51),
        'status' => '未着手',
    ])->assertSessionHasErrors(['tags' => 'タグの文字数は、50文字以下である必要があります。']);
});

test('status 正常系', function () {
    $dueDate = now()->addDay()->format('Y-m-d H:i:00');

    put("/tasks/{$this->task->id}", [
        'title' => 'ステータス正常',
        'content' => '',
        'due_date' => $dueDate,
        'tags' => '',
        'status' => '未着手',
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('tasks', [
        'id' => $this->task->id,
        'title' => 'ステータス正常',
        'content' => null,
        'due_date' => $dueDate,
        'status' => '未着手',
    ]);
});

test('status 異常系', function () {
    put("/tasks/{$this->task->id}", [
        'title' => 'ステータス空',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '',
        'status' => '',
    ])->assertSessionHasErrors(['status' => '状態は必須項目です。']);

    put("/tasks/{$this->task->id}", [
        'title' => 'ステータス長すぎ',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '',
        'status' => Str::random(51),
    ])->assertSessionHasErrors(['status' => '状態の文字数は、50文字以下である必要があります。']);

    put("/tasks/{$this->task->id}", [
        'title' => 'ステータス定義外',
        'content' => '',
        'due_date' => now()->addDay()->format('Y-m-d H:i'),
        'tags' => '',
        'status' => 'キャンセル',
    ])->assertSessionHasErrors(['status' => '選択された状態は、有効ではありません。']);
});
