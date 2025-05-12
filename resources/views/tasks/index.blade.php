<h1>タスク一覧</h1>

<ul>
@foreach($tasks as $task)
    <li>
        {{ $task->title }}（{{ $task->due_date }}）
        <a href="{{ route('tasks.edit', $task->id) }}">[編集]</a>
    </li>
@endforeach
</ul>
