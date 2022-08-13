<?php

namespace App\Http\Requests\App;

class StoreDevRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'uri' => 'string',
            'model' => 'string',
            'description' => 'string',
            'resources' => 'array', //['index','show','store','update','destroy']
            'schema' => 'string', // Migration Schema
        ];
    }
}
