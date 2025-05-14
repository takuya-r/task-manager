<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

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
