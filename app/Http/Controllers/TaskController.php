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
        // 1. バリデーション済みのデータでタスクを作成
        $task = auth()->user()->tasks()->create($request->validated());

        // 2. タグ入力（コンマ区切り）の処理と中間テーブルへの保存
        $this->syncTags($task, $request->input('tags'));

        // 3. 一覧ページへリダイレクト
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

    private function syncTags(Task $task, string $tagInput): void
    {
        if (!$tagInput) return;

        // タグ文字列を分割して整形
        $tagNames = array_filter(array_map('trim', explode(',', $tagInput)));
        $tagIds = [];

        foreach ($tagNames as $name) {
            $tag = \App\Models\Tag::firstOrCreate(['name' => $name]);
            $tagIds[] = $tag->id;
        }

        // 多対多リレーションの同期（中間テーブルへの保存）
        $task->tags()->sync($tagIds);
    }
}
