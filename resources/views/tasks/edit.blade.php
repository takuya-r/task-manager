<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('タスク編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md sm:rounded-lg p-6">
                {{-- 全体のエラーメッセージリスト（任意） --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- タイトル（必須） -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            タイトル <span class="text-red-500">*</span>
                            <span class="ml-2 inline-block bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded">必須</span>
                        </label>
                        <input type="text" name="title"
                               value="{{ old('title', $task->title) }}"
                               class="w-full rounded-md shadow-sm focus:ring focus:ring-blue-200
                               border @error('title') border-red-500 @else border-gray-300 @enderror">
                        @error('title')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 内容（任意） -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            内容
                            <span class="ml-2 inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded">任意</span>
                        </label>
                        <textarea name="content"
                                  class="w-full rounded-md shadow-sm focus:ring focus:ring-blue-200
                                  border @error('content') border-red-500 @else border-gray-300 @enderror"
                                  rows="4">{{ old('content', $task->content) }}</textarea>
                        @error('content')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 締切日（必須） -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            締切日 <span class="text-red-500">*</span>
                            <span class="ml-2 inline-block bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded">必須</span>
                        </label>
                        <input type="datetime-local" name="due_date"
                               value="{{ old('due_date', \Carbon\Carbon::parse($task->due_date)->format('Y-m-d\TH:i')) }}"
                               class="w-full rounded-md shadow-sm focus:ring focus:ring-blue-200
                               border @error('due_date') border-red-500 @else border-gray-300 @enderror">
                        @error('due_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- タグ（任意） -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            タグ（カンマ区切りで入力）
                            <span class="ml-2 inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded">任意</span>
                        </label>
                        <input type="text" name="tags"
                               value="{{ old('tags', implode(',', $task->tags->pluck('name')->toArray())) }}"
                               class="w-full rounded-md shadow-sm focus:ring focus:ring-blue-200
                               border @error('tags') border-red-500 @else border-gray-300 @enderror">
                        @error('tags')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 状態（任意） -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">
                            状態
                            <span class="ml-2 inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded">任意</span>
                        </label>
                        <select name="status"
                                class="w-full rounded-md shadow-sm focus:ring focus:ring-blue-200
                                border @error('status') border-red-500 @else border-gray-300 @enderror">
                            @foreach(config('constants.task_statuses') as $value)
                                <option value="{{ $value }}" {{ old('status', $task->status) === $value ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
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
