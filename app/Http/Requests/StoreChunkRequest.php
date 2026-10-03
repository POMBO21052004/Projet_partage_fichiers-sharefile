<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChunkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxChunkSizeKb = ceil((config('resumable_uploads.chunk_size', 8388608) * 1.1) / 1024);
        
        return [
            'chunk'        => ['required', 'file', 'max:' . $maxChunkSizeKb],
            'chunk_index'  => ['required', 'integer', 'min:0'],
            'total_chunks' => ['required', 'integer', 'min:1'],
            'total_size'   => ['required', 'integer', 'min:1'],
            'filename'     => ['required', 'string', 'max:500'],
            'checksum'     => ['nullable', 'string', 'max:128'],
        ];
    }
}
