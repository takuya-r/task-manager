<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        // ログイン中のユーザーに紐づくタスクを取得
        $tasks = auth()->user()->tasks;

        // tasks/index.blade.php に tasks を渡して表示
        return view('tasks.index', compact('tasks'));
    }

    public function store(TaskRequest $request)
    {
        auth()->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index');
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function edit(Task $task)
    {
        // 認可（他人のタスクを編集できないように）
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        return view('tasks.edit', compact('task'));
    }

    public function update(TaskRequest $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->update($request->validated());

        return redirect()->route('tasks.index')->with('message', 'タスクを更新しました');
    }

    public function destroy(Task $task)
    {
        // 自分のタスクのみ削除できる
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        // タスクを削除
        $task->delete();

        return redirect()->route('tasks.index')->with('message', 'タスクを削除しました');
    }
}
