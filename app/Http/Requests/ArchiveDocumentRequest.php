<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArchiveDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageDocuments');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'in:administrative,contract,legal,permit,license,compliance,partnership,financial,operational,other'],
            'department' => ['nullable', 'string', 'max:255'],
            'owner_email' => ['nullable', 'email'],
            'confidentiality' => ['required', 'in:general,restricted,confidential'],
            'document_date' => ['nullable', 'date'],
            'expiration_date' => ['nullable', 'date', 'after_or_equal:document_date'],
            'status' => ['required', 'in:active,needs_review,archived,superseded'],
            'linked_contract_id' => ['nullable', 'exists:contracts,id'],
            'file' => ['nullable', 'file', 'max:20480'],
        ];
    }
}
