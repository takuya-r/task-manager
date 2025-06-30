<?php

use App\Models\User;
use App\Models\Task;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->task = Task::factory()->create(['user_id' => $this->user->id]);
});

// 権限: 自分のタスクのみ編集可能
test('自分のタスクの編集ページにアクセスできる', function () {
    actingAs($this->user);

    $response = get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $response->assertSee($this->task->title);
    $response->assertSee($this->task->content);
});

// 他人のタスク: アクセスすると403になる
test('他人のタスクの編集ページにアクセスすると403になる', function () {
    $otherUser = User::factory()->create();
    $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

    actingAs($this->user);

    $response = get("/tasks/{$otherTask->id}/edit");

    $response->assertStatus(403);
});

// 反映: 編集フォームに現在のタスク内容が反映されている
test('編集ページにタスク内容が正しく反映されている', function () {
    actingAs($this->user);

    $response = get("/tasks/{$this->task->id}/edit");

    $response->assertSee('value="' . $this->task->title . '"', false);
    $response->assertSee($this->task->content);
    $response->assertSee($this->task->due_date->format('Y-m-d\TH:i'));
});
