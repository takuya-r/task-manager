<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskReminderMail;

class SendTaskReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-task-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '締切が近い又は過ぎた未完了タスクを持つユーザーにメールで通知します';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $doneStatus = config('constants.task_statuses.done');
        $notificationDays = config('constants.notification_days');
        $users = User::all();

        foreach ($users as $user) {
            $tasks = Task::where('user_id', $user->id)
                ->where('is_completed', false)
                ->whereNotNull('due_date')
                ->where('status', '!=', $doneStatus) // または `status` が `未完了` など
                ->whereDate('due_date', '<=', now()->addDays($notificationDays)) // 3日以内 or 過去
                ->orderBy('due_date', 'asc')
                ->get();

            if ($tasks->isNotEmpty()) {
                Mail::to($user->email)->send(new TaskReminderMail($user, $tasks));
                $this->info("通知を送信しました：{$user->email}");
            } else {
                $this->info("通知対象なし：{$user->email}");
            }
        }

        $this->info('全ユーザーへの通知が完了しました。');
    }
}
