<?php

namespace App\Http\Requests\App;

class UpdateMenuRequest extends FormRequest
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
            'code' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'icon' => 'required|string',
            'routes' => 'required|array',
            'routes.*.url' => 'required|string',
            'routes.*.permission' => 'required|string',
        ];
    }
}
