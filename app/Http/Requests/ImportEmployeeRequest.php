<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
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
