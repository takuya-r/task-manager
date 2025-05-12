<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Task extends Model
{
    // 保存可能なカラムの定義（マスアサインメント対策）
    protected $fillable = [
        'title',
        'content',
        'due_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
