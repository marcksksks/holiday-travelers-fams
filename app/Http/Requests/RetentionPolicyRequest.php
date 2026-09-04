<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RetentionPolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ->can('manageRetention');
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'record_category' => [
                'required',
                'in:administrative,contract,legal,permit,license,compliance,partnership,financial,operational,other',
            ],

            'retention_years' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'legal_basis' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'boolean',
            ],
        ];
    }
}