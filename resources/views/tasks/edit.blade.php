<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('タスク編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md sm:rounded-lg p-6">
                <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- タイトル -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            タイトル
                        </label>
                        <input type="text" name="title"
                               value="{{ old('title', $task->title) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                    </div>

                    <!-- 内容 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            内容
                        </label>
                        <textarea name="content"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                                  rows="4">{{ old('content', $task->content) }}</textarea>
                    </div>

                    <!-- 締切日 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            締切日
                        </label>
                        <input type="datetime-local" name="due_date"
                               value="{{ old('due_date', \Carbon\Carbon::parse($task->due_date)->format('Y-m-d\TH:i')) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                    </div>

                    <!-- 状態 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            状態
                        </label>
                        <select name="status"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                            <option value="未着手" {{ $task->status === '未着手' ? 'selected' : '' }}>未着手</option>
                            <option value="進行中" {{ $task->status === '進行中' ? 'selected' : '' }}>進行中</option>
                            <option value="完了"   {{ $task->status === '完了' ? 'selected' : '' }}>完了</option>
                        </select>
                    </div>

                    <!-- 送信ボタン -->
                    <div>
                        <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            更新
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
