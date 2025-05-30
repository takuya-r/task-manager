<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Tag;
use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // 現在ログインしているユーザーを取得
        $user = auth()->user();

        // リクエストから「tag」というクエリパラメータを取得（例：/tasks?tag=仕事）
        $tagName = $request->input('tag');

        // ユーザーに紐づくタスクをベースにクエリビルダーを生成（タグ情報も一緒に取得）
        $query = $user->tasks()->with('tags');

        // タグ名が指定されている場合は、該当タグを持つタスクだけに絞り込む
        if ($tagName) {
            $query->whereHas('tags', function ($q) use ($tagName) {
                $q->where('name', $tagName); // タグの名前が一致するものを絞り込み
            });
        }

        // 最終的なタスク一覧を取得
        $tasks = $query->get();

        // タグ一覧をすべて取得（セレクトボックス用）
        $allTags = Tag::all();

        // タスク一覧ビューにデータを渡して表示
        return view('tasks.index', compact('tasks', 'allTags', 'tagName'));
    }

    public function store(TaskRequest $request)
    {
        $todoStatus = config('constants.task_statuses.todo');

        // 1. バリデーション済みのデータを取得
        $validated = $request->validated();

        // 2. ステータスを強制的に「未着手」に設定
        $validated['status'] = $todoStatus;

        // 3. タスク作成
        $task = auth()->user()->tasks()->create($validated);

        // 4. タグ処理（中間テーブルへの保存）
        $this->syncTags($task, $request->input('tags'));

        // 5. 一覧へリダイレクト
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
        // 認可（他人のタスクを編集できないように）
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        // タスク情報を更新
        $task->update($request->validated());

        // タグの同期（空文字含めて対応）
        $this->syncTags($task, $request->input('tags'));

        return redirect()->route('tasks.index')->with('message', __('messages.task_updated'));
    }

    public function destroy(Task $task)
    {
        // 自分のタスクのみ削除できる
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        // タスクを削除
        $task->delete();

        return redirect()->route('tasks.index')->with('message', __('messages.task_deleted'));
    }

    private function syncTags(Task $task, ?string $tagInput): void
    {
        if (!$tagInput) {
            // タグ入力が空 → すべてのタグを解除
            $task->tags()->detach();
            return;
        }

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
