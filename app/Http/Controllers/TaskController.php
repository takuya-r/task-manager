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
}
