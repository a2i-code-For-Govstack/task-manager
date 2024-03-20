<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'task_title_en' => 'required',
            'task_title_bn' => 'nullable',
            'task_description' => 'nullable',
            'start_date' => 'nullable',
            'start_time' => 'nullable',
            'end_date' => 'nullable',
            'end_time' => 'nullable',
            'task_organizer' => 'required',
            'task_to_event' => 'nullable|bool',
        ];
    }
}
