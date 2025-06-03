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
    public function filterByTag(Request $request)
    {
        // \Log::debug('filterByTag');
        $user = auth()->user();
        // \Log::debug('$user:' . $user);
        $tagId = $request->input('tag');
        // \Log::debug('$tagId:' . $tagId);
        $query = $user->tasks()->with('tags');
        // \Log::debug($query->get()->toArray());
        if ($tagId) {
            // \Log::debug('if ($tagId)内　通った');
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tag_id', $tagId);
            });
        }

        $tasks = $query->get();
        // \Log::debug('$tasks:' . $tasks);
        // \Log::debug('response()->json($tasks)' . response()->json($tasks));
        return response()->json($tasks);
    }

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
