<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('タスク一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
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

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <ul class="space-y-4">
                        @forelse($tasks as $task)
                            <li class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <span class="font-medium">{{ $task->title }}</span>
                                    <span class="text-sm text-gray-500 ml-2">（{{ $task->due_date }}）</span>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <!-- 編集リンク -->
                                    <a href="{{ route('tasks.edit', $task->id) }}"
                                       class="text-blue-600 hover:underline">
                                        編集
                                    </a>

                                    <!-- 削除フォーム -->
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                          onsubmit="return confirm('本当に削除しますか？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:underline">
                                            削除
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="text-gray-500">タスクが存在しません。</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
