<?php

use App\Models\User;
use App\Models\Task;
use App\Mail\TaskReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    config()->set('constants.task_statuses.done', '完了');
    config()->set('constants.notification_days', 3);
    Mail::fake(); // メール送信をモック
});

test('締切が近い未完了タスクを持つユーザーにはメールが送られる', function () {
    $user = User::factory()->create();

    Task::factory()->create([
        'user_id' => $user->id,
        'status' => '未着手',
        'due_date' => Carbon::now()->addDay(), // 通知対象
    ]);

    Artisan::call('app:send-task-reminder');

    Mail::assertSent(TaskReminderMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email)
            && $mail->user->is($user)
            && $mail->tasks->count() === 1;
    });
});

test('通知対象タスクがない場合はメールが送られない', function () {
    $user = User::factory()->create();

    Task::factory()->create([
        'user_id' => $user->id,
        'status' => '完了', // 完了タスクなので通知対象外
        'due_date' => Carbon::now()->addDay(),
    ]);

    Artisan::call('app:send-task-reminder');

    Mail::assertNothingSent();
});

test('期限が遠いタスクも通知対象外になる', function () {
    $user = User::factory()->create();

    Task::factory()->create([
        'user_id' => $user->id,
        'status' => '未着手',
        'due_date' => Carbon::now()->addDays(10), // 通知対象外
    ]);

    Artisan::call('app:send-task-reminder');

    Mail::assertNothingSent();
});
