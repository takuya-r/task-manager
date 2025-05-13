<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('タスク一覧') }}
        </h2>
    </x-slot>

    <div x-data="{ showModal: false, selectedTask: null }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('message'))
                <div class="mb-4 text-green-600 font-semibold">
                    {{ session('message') }}
                </div>
            @endif

            <!-- 遷移ボタン -->
            <div class="mt-4 mb-4 flex justify-start">
                <a href="{{ route('tasks.create') }}"
                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    新しいタスクを追加
                </a>
            </div>

            <div class="bg-white shadow overflow-x-auto sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状態</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">タスク名</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">内容</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">締切日</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tasks as $task)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $task->status }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap max-w-xs truncate">
                                    {{ $task->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap max-w-xs truncate">
                                    <div class="flex items-center space-x-2">
                                        <span class="truncate max-w-[200px] inline-block">{{ $task->content }}</span>
                                        <button 
                                            @click="selectedTask = {{ json_encode($task) }}; showModal = true"
                                            class="text-sm text-blue-500 hover:underline">
                                            詳細
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('Y/m/d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex space-x-4">
                                    <a href="{{ route('tasks.edit', $task->id) }}"
                                       class="text-blue-600 hover:underline">編集</a>
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                          onsubmit="return confirm('本当に削除しますか？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:underline">
                                            削除
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-gray-500 text-center">
                                    タスクが存在しません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

           <!-- モーダル -->
            <div 
                x-show="showModal" 
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                x-cloak>
                <div class="bg-white p-6 rounded shadow-lg max-w-md w-full max-h-[80vh] overflow-y-auto">
                    <h2 class="text-lg font-semibold mb-4">タスク詳細</h2>
                    <template x-if="selectedTask">
                        <div class="space-y-2 break-words">
                            <p><strong>状態:</strong> <span x-text="selectedTask.status"></span></p>
                            <p><strong>タイトル:</strong> <span x-text="selectedTask.title"></span></p>
                            <p><strong>内容:</strong> 
                                <span 
                                    x-text="selectedTask.content"
                                    class="block whitespace-pre-wrap break-words"
                                ></span>
                            </p>
                            <p><strong>締切日:</strong> <span x-text="selectedTask.due_date"></span></p>
                        </div>
                    </template>
                    <div class="mt-4 text-right">
                        <button @click="showModal = false"
                                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            閉じる
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
