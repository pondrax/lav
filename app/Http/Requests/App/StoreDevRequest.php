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
            'methods' => 'required|array', //['index','show','store','update','destroy']
            // 'methods' => 'required|string',
            'schema' => 'required|string', // Migration Schema
            'force' => 'boolean', // Migration Schema
        ];
    }

    public function bodyParameters()
    {
        return [
            'uri' => [
                'description' => 'Uri Path / Model Name',
                'example'   => 'post'
            ],
            'methods' => [
                'description' => 'Available Methods',
                'example'   => ['index','show','store','update','destroy']
            ],
            'schema' => [
                'description' => 'Generate Migration with schema',
                'example' => 'title:string, body:text, slug:string:unique, published_at:date'
            ],
            'force' => [
                'description' => 'Force Generate',
                'example'   => false
            ]
        ];
    }
}
