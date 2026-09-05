<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageFacilities');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'facility_type' => ['required', 'in:conference_room,meeting_room,training_room,function_room,vehicle,other'],
            'status' => ['required', 'in:available,maintenance,unavailable,archived'],
            'equipment' => ['nullable', 'array'],
            'equipment.*' => ['string'],
        ];
    }
}
