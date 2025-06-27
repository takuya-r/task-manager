<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->routeIs('api.tasks.updateStatus') || $this->routeIs('api.tasks');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if($this->routeIs('api.tasks.updateStatus')){
            // ステータス一覧（'未着手', '進行中', '完了'）を取得
            $validStatuses = array_values(config('constants.task_statuses'));
            $statusRule = 'required|string|max:50|in:' . implode(',', $validStatuses);
            
            $rules['status'] = $statusRule;
        }
        
        if($this->routeIs('api.tasks')){
            $rules['tag'] = 'nullable|integer|exists:tags,id';
        }

        return $rules;
    }
}
