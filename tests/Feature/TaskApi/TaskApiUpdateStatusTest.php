<?php

use App\Models\User;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    $this->task = Task::factory()->create(['user_id' => $this->user->id]);
    $this->url = "/api/tasks/{$this->task->id}/status";
});

test('正常系: ステータスを正常に変更できる', function () {
    $response = $this->patchJson($this->url, ['status' => '進行中']);

    $response->assertOk();
    $response->assertJson([
        'message' => 'ステータスが更新されました',
    ]);
    $this->assertEquals('進行中', $this->task->fresh()->status);
});

test('異常系: ステータスが空の場合、バリデーションエラー', function () {
    $originalStatus = $this->task->status;

    $response = $this->patchJson($this->url, ['status' => '']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['status']);
    $this->assertEquals($originalStatus, $this->task->fresh()->status);
});

test('異常系: ステータスが51文字以上の場合、バリデーションエラー', function () {
    $longStatus = Str::random(51);
    $originalStatus = $this->task->status;

    $response = $this->patchJson($this->url, ['status' => $longStatus]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['status']);
    $this->assertEquals($originalStatus, $this->task->fresh()->status);
});

test('異常系: 無効なステータス「キャンセル」の場合、バリデーションエラー', function () {
    $originalStatus = $this->task->status;

    $response = $this->patchJson($this->url, ['status' => 'キャンセル']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['status']);
    $this->assertEquals($originalStatus, $this->task->fresh()->status);
});

test('異常系: 他人のタスクに対してステータス変更しようとすると403エラー', function () {
    $otherUser = User::factory()->create();
    $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->patchJson("/api/tasks/{$otherTask->id}/status", [
        'status' => '進行中',
    ]);

    $response->assertStatus(403); // アクセス拒否を期待
});