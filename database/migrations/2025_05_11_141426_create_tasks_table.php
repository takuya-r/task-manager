<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign Key

            $table->string('title');              // タスク名
            $table->text('content')->nullable();  // 内容（nullableでもOKなら）
            $table->dateTime('due_date');         // 締切
            $table->string('status');             // 状態（未着手、進行中など）

            $table->timestamps(); // created_at, updated_at 自動追加

            // 外部キー制約（users テーブルがある前提）
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
