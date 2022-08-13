<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;

class StoreBarTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => [
                'required',
                'string'
            ],
            'body' => [
                'required',
                'text'
            ],
            'slug' => [
                'required',
                'string'
            ],
            'published_at' => [
                'required',
                'date'
            ]
        ];
    }
}
