<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __('messages.dashboard_message') }}
                </div>
            </div>

            <!-- 遷移ボタン -->
            <div class="mt-6 flex space-x-4">
                <a href="{{ route('tasks.index') }}"
                   class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
                    タスク一覧へ
                </a>

                <a href="{{ route('tasks.create') }}"
                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    新しいタスクを追加
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
