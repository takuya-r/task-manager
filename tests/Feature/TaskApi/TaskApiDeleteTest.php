<?php

use App\Models\User;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

test('正常系: 自分のタスクを削除できる', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertOk(); // または204なら ->assertNoContent()
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('異常系: 他人のタスクを削除しようとすると403エラー', function () {
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(403);
    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
});

test('DB確認: 削除後はデータベースから物理削除されている', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $this->deleteJson("/api/tasks/{$task->id}");

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
