<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportSparePartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File harus dipilih',
            'file.file' => 'Harus berupa file',
            'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV',
            'file.max' => 'Ukuran file tidak boleh lebih dari 10MB',
        ];
    }
}
