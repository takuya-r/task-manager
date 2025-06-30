<?php
use App\Models\User;
use App\Models\Task;
use App\Models\Tag;

test('ログインユーザーのタスクのみが表示されること（全項目＋タグ含む）', function () {
    // ユーザーAとBを作成
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    // タグを作成
    $tag1 = Tag::factory()->create(['name' => 'タグA']);
    $tag2 = Tag::factory()->create(['name' => 'タグB']);
    $tag3 = Tag::factory()->create(['name' => 'タグC']);

    // ユーザーAのタスク（表示されるべき）
    $taskA = Task::factory()->create([
        'user_id' => $userA->id,
        'title' => 'Aのタイトル',
        'content' => 'Aの内容',
        'due_date' => '2030-12-31',
        'status' => '未着手',
    ]);
    $taskA->tags()->attach([$tag1->id, $tag2->id]);

    // ユーザーBのタスク（表示されてはいけない）
    $taskB = Task::factory()->create([
        'user_id' => $userB->id,
        'title' => 'Bのタイトル',
        'content' => 'Bの内容',
        'due_date' => '2030-01-01',
        'status' => '完了',
    ]);
    $taskB->tags()->attach([$tag3->id]);

    // ログインしてタスク一覧にアクセス
    $response = $this->actingAs($userA)->get('/tasks');

    $response->assertStatus(200);

    // ユーザーAのタスク内容がすべて表示されていること
    $response->assertSee('Aのタイトル');
    $response->assertSee('Aの内容');
    $response->assertSee('2030-12-31');
    $response->assertSee('タグA');
    $response->assertSee('タグB');
    // 正規表現で status="未着手" selected を検出
    expect($response->getContent())->toMatch('/<option\s+value="未着手"\s+selected[^>]*>[\s\S]*?未着手[\s\S]*?<\/option>/');

    // ユーザーBのタスク内容やタグが表示されていないこと
    $response->assertDontSee('Bのタイトル');
    $response->assertDontSee('Bの内容');
    $response->assertDontSee('2030-01-01');
    $response->assertDontSee('タグC');
    // 正規表現で status="完了" selected を検出されないことを確認
    expect($response->getContent())->not->toMatch('/<option\s+value="完了"\s+selected[^>]*>[\s\S]*?完了[\s\S]*?<\/option>/');
});

test('タスク0件のときのメッセージが適切であること', function () {
    $user = User::factory()->create();

    // タスク未作成の状態でアクセス
    $response = $this->actingAs($user)->get('/tasks');

    // 表示されるメッセージを確認
    $response->assertSee('タスクが存在しません。');
});