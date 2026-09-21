<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visitor_name' => ['required', 'string', 'max:255'],
            'visitor_organization' => ['nullable', 'string', 'max:255'],
            'visitor_email' => ['nullable', 'email'],
            'visitor_contact' => ['nullable', 'string', 'max:50'],
            'visitor_type' => ['required', 'in:customer,business_partner,supplier,government,applicant,guest,other'],
            'host_email' => ['nullable', 'email'],
            'host_name' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => [
                'required_with:facility_id',
                'nullable',
                'date_format:H:i',
                'after:start_time',
            ],
            'purpose' => ['nullable', 'string'],
            'facility_id' => ['nullable', 'exists:facilities,id'],
            'notes' => ['nullable', 'string'],
            'status' => [
                'sometimes',
                'required',
                'in:scheduled,confirmed,checked_in,completed,cancelled,no_show',
            ],
        ];
    }
}
