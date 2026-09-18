<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LegalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageLegal');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'record_type' => ['required', 'in:permit,license,legal_case,requirement,legal_document'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'issuing_authority' => ['nullable', 'string', 'max:255'],
            'issue_date' => ['nullable', 'date'],
            'expiration_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'status' => ['required', 'in:active,pending,expiring_soon,expired,renewed,closed'],
            'responsible_officer_email' => ['nullable', 'email'],
            'legal_notes' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'file' => \App\Support\DocumentUploadPolicy::rules(),
        ];
    }
}
