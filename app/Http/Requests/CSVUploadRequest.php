<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CSVUploadRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'old_csv' => 'required|mimes:csv,txt|max:500',
            'new_csv' => 'required|mimes:csv,txt|max:500'
        ];
    }
}
