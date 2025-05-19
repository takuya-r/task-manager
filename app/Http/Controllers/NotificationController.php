<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $doneStatus = config('constants.task_statuses.done');
        $notificationDays = config('constants.notification_days');

        $tasks = Task::where('user_id', Auth::id())
            ->where('status', '!=', $doneStatus) // または `status` が `未完了` など
            ->whereDate('due_date', '<=', now()->addDays($notificationDays)) // 3日以内 or 過去
            ->orderBy('due_date', 'asc')
            ->get(['id', 'title', 'due_date']);

        return response()->json($tasks);
    }
}
