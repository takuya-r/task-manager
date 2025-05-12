<h1>タスク一覧</h1>

@if (session('message'))
    <div style="color: green;">
        {{ session('message') }}
    </div>
@endif

<ul>
    @foreach($tasks as $task)
        <li>
            {{ $task->title }}（{{ $task->due_date }}）
            
            <!-- 編集リンク -->
            <a href="{{ route('tasks.edit', $task->id) }}">[編集]</a>
            
            <!-- 削除リンク（DELETEフォーム） -->
            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('本当に削除しますか？')">削除</button>
            </form>
        </li>
    @endforeach
</ul>
