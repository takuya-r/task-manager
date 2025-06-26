<?php
use App\Models\Tag;
use App\Models\User;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;

test('タグ検索で選択したタグのタスクのみを取得できること',function() {
    $user = User::factory()->create();
    Sanctum::actingAs($user); // Laravel Sanctumでの認証

    // タグを2つ作成
    $tag1 = Tag::factory()->create(['name' => '勉強']);
    $tag2 = Tag::factory()->create(['name' => '趣味']);

    // タスクを3件作成し、タグを紐づける
    $taskWithTag1 = Task::factory()->create(['user_id' => $user->id]);
    $taskWithTag1->tags()->attach($tag1->id);

    $taskWithTag2 = Task::factory()->create(['user_id' => $user->id]);
    $taskWithTag2->tags()->attach($tag2->id);

    $taskWithoutTag = Task::factory()->create(['user_id' => $user->id]);
    // タグなし

    // タグ1（勉強）でフィルターリクエスト送信
    $response = getJson(route('api.tasks', ['tag' => $tag1->id]));

    $response->assertStatus(200);

    $data = $response->json();

    // ✅ 結果は1件のみ
    $this->assertCount(1, $data);

    // ✅ 必須フィールドを検証
    $this->assertArrayHasKey('title', $data[0]);
    $this->assertEquals($taskWithTag1->id, $data[0]['id']);

    // ✅ タグが「勉強」であることを検証
    $this->assertArrayHasKey('tags', $data[0]);
    $this->assertEquals('勉強', $data[0]['tags'][0]['name']);

});