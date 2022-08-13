<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Http\UploadedFile;

class FormRequest extends BaseFormRequest
{
    protected $uploadedPath = null;

    protected $uploadedTypeString = true;

    public function mergeValidation()
    {
        return [];
    }

    public function validated($key = null, $default = null)
    {
        $validatedData = array_merge(parent::validated($key, $default), $this->mergeValidation());
        if (! $key) {
            return $this->withUploadedFile($validatedData);
        }

        return $validatedData;
    }

    public function withUploadedFile($validatedData)
    {
        return collect($validatedData)->map(function ($data, $key) {
            if (isset($this->rules()[$key])
                && collect($this->rules()[$key])->hasAny(['file', 'mimes'])
                && $data instanceof UploadedFile) {
                $this->storeUploadedFile($data, $key);
            }

            return $data;
        })->all();
    }

    public function storeUploadedFile(&$data, $key)
    {
        $path = $data->store($this->uploadedPath.'/'.$key);
        if ($this->uploadedTypeString) {
            $data = $path;
        }
    }

    public function uploadedAsObject()
    {
        $this->uploadedTypeString = false;
    }

    public function uploadedAsString()
    {
        $this->uploadedTypeString = true;
    }

    public function bodyParameters()
    {
        return [
            //
        ];
    }
}
