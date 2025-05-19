<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $now = Carbon::now();

        $tasks = Task::where('user_id', Auth::id())
            ->where('status', '!=', '完了') // または `status` が `未完了` など
            ->whereDate('due_date', '<=', $now->copy()->addDays(3)) // 3日以内 or 過去
            ->orderBy('due_date', 'asc')
            ->get(['id', 'title', 'due_date']);

        return response()->json($tasks);
    }
}
