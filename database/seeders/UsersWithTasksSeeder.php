<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
//use Illuminate\Support\Facades\Hash;
use App\Models\User;
//use App\Models\Task;
use App\Models\Tag;

class UsersWithTasksSeeder extends Seeder
{
    public function run(): void
    {
        // タグを作成（重複チェックしてから作成）
        $tagNames = [
            '会議', '開発', 'デザイン', '資料作成', 'レビュー',
            'テスト', '高優先', '要対応', '外部連携', 'リモート作業',
        ];

        $tags = collect($tagNames)->map(function ($name) {
            return Tag::firstOrCreate(['name' => $name]);
        });

        // ユーザー1（太郎）
        $user1 = User::factory()->create([
            'name' => 'テスト太郎',
            'email' => 'taro@example.com',
            'password' => bcrypt('password'),
        ]);

        $user1Tasks = [
            ['title' => '会議資料の作成', 'content' => '来週の会議で使用する資料を作成します。必要なデータを集めて、PowerPointでまとめます。'],
            ['title' => '仕様書レビュー', 'content' => '新規開発の仕様書を確認し、懸念点をまとめてフィードバックを返します。'],
            ['title' => '週次報告作成', 'content' => '1週間の作業実績と進捗状況をまとめ、上司に提出します。'],
        ];

        foreach ($user1Tasks as $taskData) {
            $task = $user1->tasks()->create([
                ...$taskData,
                'due_date' => now()->addDays(rand(1, 5)),
                'status' => config('constants.task_statuses.todo'),
            ]);

            // ランダムで2～4個のタグをタスクに付与
            $randomTags = $tags->random(rand(2, 4))->pluck('id')->toArray();
            $task->tags()->attach($randomTags);
        }

        // ユーザー2（花子）
        $user2 = User::factory()->create([
            'name' => 'テスト花子',
            'email' => 'hanako@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2Tasks = [
            ['title' => 'デザイン修正', 'content' => 'UIデザインの細かい修正を行います。特にボタンサイズや色合いの統一に注意します。'],
            ['title' => 'ユーザーテスト準備', 'content' => '社内ユーザー向けのテストシナリオを作成し、テスト環境の準備を行います。'],
            ['title' => 'FAQ更新', 'content' => 'ユーザーからの問い合わせに対応するため、よくある質問ページを最新情報に更新します。'],
        ];

        foreach ($user2Tasks as $taskData) {
            $task = $user2->tasks()->create([
                ...$taskData,
                'due_date' => now()->addDays(rand(2, 6)),
                'status' => config('constants.task_statuses.todo'),
            ]);

            // ランダムで2～4個のタグをタスクに付与
            $randomTags = $tags->random(rand(2, 4))->pluck('id')->toArray();
            $task->tags()->attach($randomTags);
        }
    }
}