<h1>タスク編集</h1>
<form action="{{ route('tasks.update', $task->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>タイトル:
        <input type="text" name="title" value="{{ old('title', $task->title) }}">
    </label><br>

    <label>内容:
        <textarea name="content">{{ old('content', $task->content) }}</textarea>
    </label><br>

    <label>締切日:
        <input type="datetime-local" name="due_date"
               value="{{ old('due_date', \Carbon\Carbon::parse($task->due_date)->format('Y-m-d\TH:i')) }}">
    </label><br>

    <label>状態:
        <select name="status">
            <option value="未着手" {{ $task->status === '未着手' ? 'selected' : '' }}>未着手</option>
            <option value="進行中" {{ $task->status === '進行中' ? 'selected' : '' }}>進行中</option>
            <option value="完了"   {{ $task->status === '完了' ? 'selected' : '' }}>完了</option>
        </select>
    </label><br>

    <button type="submit">更新</button>
</form>
