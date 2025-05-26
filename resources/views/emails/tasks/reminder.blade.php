<x-mail::message>
# {{ $user->name }} さんへ

以下のタスクが期限間近または期限を過ぎています。対応をお願いいたします。

@foreach ($tasks as $task)
@php
    $due = \Carbon\Carbon::parse($task->due_date);
    $now = \Carbon\Carbon::now();
@endphp

- **{{ $task->title }}**
  （締切：
  @if ($due->lt($now))
    <span style="color: red;">{{ $due->format('Y年m月d日') }}【期限超過】</span>
  @else
    <span style="color: orange;">{{ $due->format('Y年m月d日') }}【期限間近】</span>
  @endif
  ）

@endforeach

<x-mail::button :url="url('/tasks')">
タスク一覧を見る
</x-mail::button>

ご確認のほどよろしくお願いいたします。  
{{ config('app.name') }}
</x-mail::message>