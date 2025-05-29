<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TaskRequest;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskApiController extends Controller
{
    public function updateStatus(TaskRequest $request, Task $task)
    {
        \Log::debug('タスクのuser_id: ' . $task->user_id);
        \Log::debug('ログインユーザーのid: ' . Auth::id());
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->status = $request->input('status');
        $task->save();

        return response()->json([
            'message' => 'Status updated successfully',
            'task' => $task,
        ]);
    }
}
