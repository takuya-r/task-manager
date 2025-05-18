<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('タスク作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md sm:rounded-lg p-6">
                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- タイトル -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            タイトル
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                    </div>

                    <!-- 内容 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            内容
                        </label>
                        <textarea name="content" rows="4"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">{{ old('content') }}</textarea>
                    </div>

                    <!-- 締切日 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            締切日
                        </label>
                        <input type="datetime-local" name="due_date" value="{{ old('due_date') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                    </div>

                    <!-- タグ -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            タグ（カンマ区切り）
                        </label>
                        <input type="text" name="tags" value="{{ old('tags') }}"
                               placeholder="例: 勉強, 趣味, 重要"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                    </div>

                    <!-- 送信ボタン -->
                    <div>
                        <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            登録
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
