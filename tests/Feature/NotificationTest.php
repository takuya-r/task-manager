<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    config()->set('constants.task_statuses.done', '完了');
    config()->set('constants.notification_days', 3);

    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

test('通知対象のタスクのみが返る（未完了かつ期限が近いもの）', function () {
    $today = Carbon::today();

    // 通知対象（未完了・期限3日以内）
    $t1 = Task::factory()->create([
        'user_id' => $this->user->id,
        'status' => '未着手',
        'due_date' => $today->copy()->addDays(2),
    ]);

    // 通知対象（未完了・期限が過去）
    $t2 = Task::factory()->create([
        'user_id' => $this->user->id,
        'status' => '進行中',
        'due_date' => $today->copy()->subDay(),
    ]);

    // 対象外（完了済）
    Task::factory()->create([
        'user_id' => $this->user->id,
        'status' => '完了',
        'due_date' => $today,
    ]);

    // 対象外（期限が未来すぎる）
    Task::factory()->create([
        'user_id' => $this->user->id,
        'status' => '未着手',
        'due_date' => $today->copy()->addDays(10),
    ]);

    // 対象外（他人のタスク）
    Task::factory()->create([
        'user_id' => User::factory()->create()->id,
        'status' => '未着手',
        'due_date' => $today,
    ]);

    $response = $this->getJson('/notifications');

    $response->assertOk()
             ->assertJsonCount(2)
             ->assertJson([
                 ['id' => $t2->id, 'title' => $t2->title],
                 ['id' => $t1->id, 'title' => $t1->title],
             ]);

    // due_date順に並んでいるか確認
    $data = $response->json();
    expect(Carbon::parse($data[0]['due_date']))->lessThan(Carbon::parse($data[1]['due_date']));
});
