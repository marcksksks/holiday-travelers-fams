<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('operateVisitorDesk');
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'organization' => ['nullable', 'string', 'max:255'],
            'visitor_type' => ['required', 'in:customer,business_partner,supplier,government,applicant,guest,other'],
            'purpose' => ['nullable', 'string'],
            'host_email' => ['nullable', 'email'],
            'host_name' => ['nullable', 'string', 'max:255'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'id_reference' => ['nullable', 'string', 'max:255'],
            'is_walk_in' => ['boolean'],
            'privacy_acknowledged' => ['required', 'accepted'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
