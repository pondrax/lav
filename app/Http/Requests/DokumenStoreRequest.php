<?php

namespace App\Http\Requests;

class DokumenStoreRequest extends FormRequest
{
    protected $uploadedPath = 'public/dokumen';

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
            'file' => 'required|file',
            'nama' => 'required|string',
            'nomor' => 'string',
            'deskripsi' => 'string',
            'size' => 'int',
        ];
    }
}
