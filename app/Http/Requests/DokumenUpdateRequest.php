<?php

namespace App\Http\Requests;

class DokumenUpdateRequest extends FormRequest
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
            'nama' => 'string',
            'nomor' => 'string|nullable',
            'tanggal' => 'string|nullable',
            'deskripsi' => 'string|nullable',
            'spesimen' => 'nullable',
            'penandatangan' => 'string|nullable',
            'password' => 'string|nullable',
        ];
    }
}
