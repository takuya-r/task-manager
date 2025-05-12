<h1>タスク作成</h1>
<form action="{{ route('tasks.store') }}" method="POST">
    @csrf
    <label>タイトル: <input type="text" name="title" value="{{ old('title') }}"></label><br>
    <label>内容: <textarea name="content">{{ old('content') }}</textarea></label><br>
    <label>締切日: <input type="datetime-local" name="due_date" value="{{ old('due_date') }}"></label><br>
    <label>状態: 
        <select name="status">
            <option value="未着手">未着手</option>
            <option value="進行中">進行中</option>
            <option value="完了">完了</option>
        </select>
    </label><br>
    <button type="submit">登録</button>
</form>