<x-guest-layout>
    <div class="text-center space-y-6">
        <h1 class="text-3xl font-bold text-gray-800">タスク管理ツールへようこそ</h1>
        <p class="text-gray-600">このアプリでは、タスクの作成・管理・編集が簡単にできます。</p>

        <div class="flex justify-center space-x-4">
            <a href="{{ route('login') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                ログイン
            </a>
            <a href="{{ route('register') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                新規登録
            </a>
        </div>
    </div>
</x-guest-layout>
