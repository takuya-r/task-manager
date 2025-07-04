<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TaskApiRequest;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskApiController extends Controller
{
    public function filterByTag(TaskApiRequest $request)
    {
        $user = auth()->user();
        $tagId = $request->input('tag');
        $query = $user->tasks()->with('tags');
        if ($tagId) {
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tag_id', $tagId);
            });
        }

        $tasks = $query->get();
        return response()->json($tasks);
    }

    public function updateStatus(TaskApiRequest $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => __('messages.forbidden')], 403);
        }

        $task->status = $request->input('status');
        $task->save();

        return response()->json([
            'message' => __('messages.status_updated'),
            'task' => $task,
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        // 自分のタスクか確認
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => __('messages.forbidden')], 403);
        }

        $task->delete();

        return response()->json(['message' => __('messages.task_deleted')]);
    }
}
