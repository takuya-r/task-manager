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
                <div class="flex items-center space-x-2">
                    <label for="tag-select" class="text-sm font-medium text-gray-700">タグで絞り込み:</label>
                    <select name="tag" id="tag-select" class="border-gray-300 rounded px-2 py-1 w-48 cursor-pointer">
                        <option value="">すべて</option>
                        @foreach ($allTags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-x-auto">
                <div class="overflow-y-auto max-h-[500px]">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状態</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">タスク名</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">内容</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">締切日</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">タグ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                            </tr>
                        </thead>
                        <tbody id="task-table-body" class="bg-white divide-y divide-gray-200">
                            @forelse($tasks as $task)
                                <tr id="task-{{ $task->id }}">
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
                                        <button onclick="openTaskModal({{ $task->id }})" class="text-sm text-blue-500 hover:underline">
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
            </div>

            <!-- 詳細モーダル -->
            <div
                x-show="$store.taskModal.showModal"
                x-cloak
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
            >
                <div class="bg-white p-6 rounded shadow-lg max-w-md w-full max-h-[80vh] overflow-y-auto">
                    <h2 class="text-lg font-semibold mb-4">タスク詳細</h2>
                    <template x-if="$store.taskModal.selectedTask">
                        <div class="space-y-2 break-words">
                            <p><strong>状態:</strong> <span x-text="$store.taskModal.selectedTask.status"></span></p>
                            <p><strong>タイトル:</strong> <span x-text="$store.taskModal.selectedTask.title"></span></p>
                            <p><strong>内容:</strong>
                                <span x-text="$store.taskModal.selectedTask.content" class="block whitespace-pre-wrap break-words"></span>
                            </p>
                            <p><strong>締切日:</strong> <span x-text="$store.taskModal.selectedTask.due_date"></span></p>
                            <template x-if="$store.taskModal.selectedTask.tags && $store.taskModal.selectedTask.tags.length">
                                <div>
                                    <strong>タグ:</strong>
                                    <template x-for="tag in $store.taskModal.selectedTask.tags" :key="tag.id">
                                        <span class="inline-block bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded mr-1" x-text="tag.name"></span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div class="mt-4 text-right">
                        <button @click="$store.taskModal.showModal = false" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            閉じる
                        </button>
                    </div>
                </div>
            </div>

            <!-- 削除モーダル -->
            <div x-show="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" id="deleteModal" x-cloak>
                <div class="bg-white p-6 rounded shadow-lg max-w-sm w-full">
                    <h2 class="text-lg font-semibold mb-4">{{ __('messages.delete_confirm') }}</h2>
                    <div class="flex justify-end gap-4">
                        <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-300 rounded">キャンセル</button>
                        <button
                            @click="deleteTask(selectedDeleteId),showDeleteModal = false,selectedDeleteId = null"
                            class="px-4 py-2 bg-red-500 text-white rounded">
                            削除する
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div id="status-config" data-statuses='@json(config("constants.task_statuses"))'></div>
    @php
        // messages.php の全内容を取得（配列形式）
        $messages = __('messages');
    @endphp
    <div id="message-config" data-messages='@json($messages)'></div>
</x-app-layout>

<script>
    // 初期データの準備
    const tasks = @json($tasks);
    const statusList = JSON.parse(document.getElementById('status-config').dataset.statuses);
    window.tasksMap = new Map();
    tasks.forEach(task => window.tasksMap.set(task.id, task));

    // Alpine.js 初期化
    document.addEventListener('alpine:init', () => {
        Alpine.store('taskModal', {
            selectedTask: null,
            showModal: false
        });
    });

    // メッセージを取得する関数（再利用可能）
    window.getMessages = function() {
        const configEl = document.getElementById('message-config');
        if (!configEl) return {};
        try {
            return JSON.parse(configEl.dataset.messages || '{}');
        } catch (e) {
            console.error('Failed to parse messages:', e);
            return {};
        }
    }

    // タスク詳細モーダルを開く
    function openTaskModal(taskId) {
        const task = window.tasksMap.get(taskId);
        if (!task) return;
        window.dispatchEvent(new CustomEvent('open-task', { detail: task }));
    }

    // Alpineのストアにデータを渡してモーダル表示
    window.addEventListener('open-task', (event) => {
        const store = Alpine.store('taskModal');
        store.selectedTask = null;
        store.selectedTask = event.detail;
        store.showModal = true;
    });

    // タグによるフィルター処理
    document.getElementById('tag-select').addEventListener('change', function () {
        const selectedTag = this.value;
        fetch(`/api/tasks?tag=${encodeURIComponent(selectedTag)}`, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(updateTaskTable);
    });

    // タスクテーブルを更新する
    function updateTaskTable(tasks) {
        const tbody = document.getElementById('task-table-body');
        const messages = getMessages();
        tbody.innerHTML = tasks.length === 0
            ? `<tr><td colspan="6" class="px-6 py-4 text-gray-500 text-center">${messages.no_matching_tasks}</td></tr>`
            : tasks.map(generateTaskRow).join('');
    }

    // HTML文字列を生成する
    function generateTaskRow(task) {
        window.tasksMap.set(task.id, task);
        const tagHtml = task.tags.map(tag => `
            <span class="block max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded" title="${tag.name}">
                ${tag.name}
            </span>
        `).join('');

        return `
            <tr id="task-${task.id}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <select name="status" data-task-id="${task.id}" onchange="updateStatus(this)" class="border-gray-300 rounded px-2 py-1 text-sm w-20">
                        ${Object.entries(statusList).map(([key, label]) => `
                            <option value="${label}" ${task.status === label ? 'selected' : ''}>${label}</option>
                        `).join('')}
                    </select>
                </td>
                <td class="px-6 py-4 whitespace-nowrap truncate max-w-[200px]">${task.title}</td>
                <td class="px-6 py-4 whitespace-nowrap truncate max-w-[200px]">${task.content}</td>
                <td class="px-6 py-4 whitespace-nowrap">${task.due_date}</td>
                <td class="px-6 py-4 whitespace-nowrap w-64"><div class="flex flex-wrap gap-1 max-w-full">${tagHtml}</div></td>
                <td class="px-6 py-4 whitespace-nowrap flex space-x-4">
                    <button onclick="openTaskModal(${task.id})" class="text-sm text-blue-500 hover:underline">詳細</button>
                    <a href="/tasks/${task.id}/edit" class="text-green-600 hover:underline">編集</a>
                    <button @click="selectedDeleteId = ${task.id}; showDeleteModal = true" class="text-red-600 hover:underline">削除</button>
                </td>
            </tr>
        `;
    }
</script>