<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->routeIs('tasks.store') || $this->routeIs('tasks.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // ステータス一覧（'未着手', '進行中', '完了'）を取得
        $validStatuses = array_values(config('constants.task_statuses'));
        $statusRule = 'required|string|max:50|in:' . implode(',', $validStatuses);

        // 共通ルール
        $rules = [
            'title' => 'required|max:50',
            'content' => 'nullable|max:500',
            'due_date' => 'required|date',
            'tags' => 'nullable|string|max:50',
        ];

        // update のときのみ status をバリデーション対象に追加
        if ($this->routeIs('tasks.update')) {
            $rules['status'] = $statusRule;
        }

        return $rules;
    }
}
