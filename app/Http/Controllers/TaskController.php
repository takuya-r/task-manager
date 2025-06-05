<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Tag;
use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // ログイン中のユーザー
        $user = auth()->user();

        // ユーザーのタスクを取得
        $tasks = $user->tasks()->with('tags')->get();

        // ユーザーのタスクに紐づくタグのみ取得
        $allTags = Tag::whereHas('tasks', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // タスク一覧ビューにデータを渡して表示
        return view('tasks.index', compact('tasks', 'allTags'));
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
