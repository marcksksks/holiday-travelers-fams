<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordRetentionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageRetention');
    }

    public function rules(): array
    {
        return [
            'record_title' => ['required', 'string', 'max:255'],
            'record_type' => ['required', 'in:document,contract,legal_record,other'],
            'record_id' => ['nullable', 'integer'],
            'policy_id' => ['nullable', 'exists:retention_policies,id'],
            'start_date' => ['nullable', 'date'],
            'review_date' => ['nullable', 'date'],
            'status' => ['required', 'in:retained,review_required,extended,archived,marked_for_disposal'],
            'compliance_status' => ['required', 'in:compliant,at_risk,non_compliant'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
