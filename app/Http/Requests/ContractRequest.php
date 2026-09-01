<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageContracts');
    }

    public function rules(): array
    {
        return [
            'contract_number' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'contract_type' => ['required', 'in:hotel,tour_operator,transportation,supplier,partnership,service,other'],
            'parties' => ['nullable', 'array'],
            'parties.*' => ['string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:8'],
            'description' => ['nullable', 'string'],
            'responsible_officer_email' => ['nullable', 'email'],
            'file' => ['nullable', 'file', 'max:20480'],
        ];
    }
}
