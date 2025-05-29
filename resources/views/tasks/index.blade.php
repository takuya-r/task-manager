<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('タスク一覧') }}
        </h2>
    </x-slot>

    <div x-data="{ showModal: false, selectedTask: null, showDeleteModal: false, selectedDeleteId: null }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('message'))
                <div class="mb-4 text-green-600 font-semibold">
                    {{ session('message') }}
                </div>
            @endif

            <div id="status-message" class="mb-4 text-green-600 font-semibold"></div>

            <!-- 遷移ボタン -->
            <div class="mt-4 mb-4 flex justify-start">
                <a href="{{ route('tasks.create') }}"
                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    新しいタスクを追加
                </a>
            </div>

            <!-- タグ検索フォーム -->
            <div class="mb-4">
                <form method="GET" action="{{ route('tasks.index') }}" class="flex items-center space-x-2">
                    <label for="tag" class="text-sm font-medium text-gray-700">タグで絞り込み:</label>
                    <select name="tag" id="tag" onchange="this.form.submit()"
                            class="border-gray-300 rounded px-2 py-1 w-48 cursor-pointer">
                        <option value="">すべて表示</option>
                        @foreach ($allTags as $tag)
                            <option value="{{ $tag->name }}" {{ request('tag') === $tag->name ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="bg-white shadow overflow-x-auto sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状態</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">タスク名</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">内容</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">締切日</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">タグ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tasks as $task)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select 
                                        name="status" 
                                        data-task-id="{{ $task->id }}"
                                        onchange="updateStatus(this)"
                                        class="border-gray-300 rounded px-2 py-1 text-sm w-20"
                                    >
                                        @foreach (config('constants.task_statuses') as $status)
                                            <option value="{{ $status }}" {{ $task->status === $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap truncate max-w-[200px]">{{ $task->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap truncate max-w-[200px]">{{ $task->content }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($task->due_date)->format('Y/m/d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap w-64">
                                    <div class="flex flex-wrap gap-1 max-w-full">
                                        @foreach ($task->tags as $tag)
                                            <span
                                                class="block max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded"
                                                title="{{ $tag->name }}"
                                            >
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex space-x-4">
                                    <button
                                        @click="selectedTask = {{ json_encode([
                                                    'id' => $task->id,
                                                    'status' => $task->status,
                                                    'title' => $task->title,
                                                    'content' => $task->content,
                                                    'due_date' => $task->due_date,
                                                    'tags' => $task->tags->pluck('name')->toArray(), // ← タグ名の配列にする
                                                ]) }}; showModal = true"
                                        class="text-sm text-blue-500 hover:underline">
                                        詳細
                                    </button>
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="text-green-600 hover:underline">編集</a>
                                    <button
                                        @click="selectedDeleteId = {{ $task->id }}; showDeleteModal = true"
                                        class="text-red-600 hover:underline">
                                        削除
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-gray-500 text-center">
                                    {{ __('messages.no_tasks') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- 詳細モーダル -->
            <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" x-cloak>
                <div class="bg-white p-6 rounded shadow-lg max-w-md w-full max-h-[80vh] overflow-y-auto">
                    <h2 class="text-lg font-semibold mb-4">タスク詳細</h2>
                    <template x-if="selectedTask">
                        <div class="space-y-2 break-words">
                            <p><strong>状態:</strong> <span x-text="selectedTask.status"></span></p>
                            <p><strong>タイトル:</strong> <span x-text="selectedTask.title"></span></p>
                            <p><strong>内容:</strong>
                                <span x-text="selectedTask.content" class="block whitespace-pre-wrap break-words"></span>
                            </p>
                            <p><strong>締切日:</strong> <span x-text="selectedTask.due_date"></span></p>
                            <template x-if="selectedTask.tags && selectedTask.tags.length">
                                <div>
                                    <strong>タグ:</strong>
                                    <template x-for="tag in selectedTask.tags" :key="tag">
                                        <span class="inline-block bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded mr-1" x-text="tag"></span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div class="mt-4 text-right">
                        <button @click="showModal = false" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            閉じる
                        </button>
                    </div>
                </div>
            </div>

            <!-- 削除モーダル -->
            <div x-show="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
                <div class="bg-white p-6 rounded shadow-lg max-w-sm w-full">
                    <h2 class="text-lg font-semibold mb-4">{{ __('messages.delete_confirm') }}</h2>
                    <div class="flex justify-end gap-4">
                        <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-300 rounded">キャンセル</button>
                        <form :action="'/tasks/' + selectedDeleteId" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded">削除する</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script>
function updateStatus(selectElement) {
    const taskId = selectElement.dataset.taskId;
    const status = selectElement.value;

    fetch(`/api/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status }),
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        const msgDiv = document.getElementById('status-message');
        msgDiv.textContent = data.message || 'ステータスが更新されました';
        msgDiv.className = 'mb-4 text-green-600 font-semibold';
        setTimeout(() => msgDiv.textContent = '', 3000);
    })
    .catch(err => {
        const msgDiv = document.getElementById('status-message');
        msgDiv.textContent = 'ステータス更新に失敗しました';
        msgDiv.className = 'mb-4 text-red-600 font-semibold';
    });
}
</script>
