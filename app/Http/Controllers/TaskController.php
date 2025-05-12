<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\User;

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

}
