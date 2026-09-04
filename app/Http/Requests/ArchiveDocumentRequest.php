<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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

            'category' => [
                'required',
                'in:administrative,contract,legal,permit,license,compliance,partnership,financial,operational,other',
            ],

            'department' => ['nullable', 'string', 'max:255'],

            'container_id' => [
                'nullable',
                'integer',
                'exists:document_containers,id',
            ],
            'owner_email' => ['nullable', 'email'],

            'confidentiality' => [
                'required',
                'in:general,restricted,confidential',
            ],

            'document_date' => ['nullable', 'date'],

            'expiration_date' => [
                'nullable',
                'date',
                'after_or_equal:document_date',
            ],

            'status' => [
                'required',
                'in:active,needs_review,archived,superseded',
            ],

            'related_type' => [
                'nullable',
                Rule::in([
                    'contract',
                    'legal',
                    'reservation',
                    'visitor',
                ]),
            ],

            'related_id' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'file' => [
                'nullable',
                'file',
                'max:20480',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $type = $this->input('related_type');
                $id = $this->input('related_id');

                if (! $type && $id) {
                    $validator->errors()->add(
                        'related_type',
                        'Select the related module first.'
                    );

                    return;
                }

                if ($type && ! $id) {
                    $validator->errors()->add(
                        'related_id',
                        'Select the related record.'
                    );

                    return;
                }

                if (! $type || ! $id) {
                    return;
                }

                $permission = match ($type) {
                    'contract' => 'viewContracts',
                    'legal' => 'viewLegal',
                    'visitor' => 'viewVisitors',
                    default => null,
                };

                if (
                    $permission &&
                    ! $this->user()->can($permission)
                ) {
                    $validator->errors()->add(
                        'related_type',
                        'You do not have permission to link documents to that module.'
                    );

                    return;
                }

                $table = match ($type) {
                    'contract' => 'contracts',
                    'legal' => 'legal_records',
                    'reservation' => 'reservations',
                    'visitor' => 'visitors',
                };

                $exists = DB::table($table)
                    ->where('id', $id)
                    ->exists();

                if (! $exists) {
                    $validator->errors()->add(
                        'related_id',
                        'The selected related record does not exist.'
                    );
                }
            },
        ];
    }
}