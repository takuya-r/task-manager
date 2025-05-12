<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\User;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'nullable',
            'due_date' => 'required|date',
            'status' => 'required|string|max:50',
        ]);

        auth()->user()->tasks()->create($validated);

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

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'nullable',
            'due_date' => 'required|date',
            'status' => 'required|string|max:50',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('message', 'タスクを更新しました');
    }
}
