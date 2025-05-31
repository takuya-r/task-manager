<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TaskApiRequest;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskApiController extends Controller
{
    public function updateStatus(TaskApiRequest $request, Task $task)
    {
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

    public function destroy(Task $task): JsonResponse
    {
        // 自分のタスクか確認
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
