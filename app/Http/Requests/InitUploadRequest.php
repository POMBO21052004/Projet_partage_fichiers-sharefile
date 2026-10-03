<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filename'   => ['required', 'string', 'max:500'],
            'filesize'   => ['required', 'integer', 'min:1'],
            'mime_type'  => ['nullable', 'string', 'max:200'],
            'folder_id'  => ['nullable', 'integer', 'exists:folders,id'],
            'chunk_size' => ['nullable', 'integer', 'min:1048576', 'max:104857600'],
            'checksum'   => ['nullable', 'string', 'max:128'],
        ];
    }
}
