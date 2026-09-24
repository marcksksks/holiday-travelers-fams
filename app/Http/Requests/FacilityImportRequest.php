<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class FacilityImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can('manageFacilities')
            ?? false;
    }

    public function rules(): array
    {
        return [
            'import_file' => [
                'required',
                File::types([
                    'csv',
                    'xlsx',
                    'json',
                ])->max('5mb'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'import_file.required' => 'Choose a CSV, XLSX, or JSON file to import.',

            'import_file.mimes' => 'The import file must be CSV, XLSX, or JSON.',
        ];
    }
}
