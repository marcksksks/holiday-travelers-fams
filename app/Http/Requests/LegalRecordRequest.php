<?php

namespace App\Http\Requests;

use App\Models\LegalRecord;
use App\Models\User;
use App\Support\DocumentUploadPolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LegalRecordRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $defaults = [];

        /*
         * Backward compatibility for older create clients.
         *
         * Priority and confidentiality did not exist before
         * Legal Management v2, so omitted values receive
         * safe defaults on creation.
         */
        if ($this->isMethod('post')) {
            if ($this->missing('priority')) {
                $defaults['priority'] =
                    'medium';
            }

            if ($this->missing('confidentiality_level')) {
                $defaults['confidentiality_level'] =
                    'internal';
            }
        }

        /*
         * Backward compatibility for older update clients.
         *
         * When PUT/PATCH does not send the new fields,
         * preserve the current database values instead of
         * resetting or rejecting the record.
         */
        if (
            $this->isMethod('put') ||
            $this->isMethod('patch')
        ) {
            $legal =
                $this->route('legal');

            if ($legal instanceof LegalRecord) {
                if ($this->missing('priority')) {
                    $defaults['priority'] =
                        $legal->priority
                        ?: 'medium';
                }

                if (
                    $this->missing(
                        'confidentiality_level'
                    )
                ) {
                    $defaults['confidentiality_level'] =
                        $legal->confidentiality_level
                        ?: 'internal';
                }
            }
        }

        if ($defaults !== []) {
            $this->merge(
                $defaults
            );
        }
    }

    public function authorize(): bool
    {
        return $this->user()
            ->can('manageLegal');
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'record_type' => [
                'required',
                Rule::in([
                    'permit',
                    'license',
                    'legal_case',
                    'requirement',
                    'legal_document',
                ]),
            ],

            'legal_category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'issuing_authority' => [
                'nullable',
                'string',
                'max:255',
            ],

            'jurisdiction' => [
                'nullable',
                'string',
                'max:150',
            ],

            'legal_basis' => [
                'nullable',
                'string',
            ],

            'issue_date' => [
                'nullable',
                'date',
            ],

            'expiration_date' => [
                'nullable',
                'date',
                'after_or_equal:issue_date',
            ],

            'due_date' => [
                'nullable',
                'date',
            ],

            'next_action_date' => [
                'nullable',
                'date',
            ],

            'next_action' => [
                'nullable',
                'string',
                'max:500',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'pending',
                    'expiring_soon',
                    'expired',
                    'renewed',
                    'closed',
                ]),
            ],

            'priority' => [
                'required',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                    'critical',
                ]),
            ],

            'confidentiality_level' => [
                'required',
                Rule::in([
                    'internal',
                    'confidential',
                    'restricted',
                ]),
            ],

            'assigned_user_id' => [
                'nullable',

                Rule::exists(
                    'users',
                    'id'
                )
                    ->where(
                        fn ($query) => $query
                            ->where(
                                'is_active',
                                true
                            )
                            ->whereIn(
                                'app_role',
                                [
                                    User::ROLE_ADMIN_OFFICER,
                                    User::ROLE_LEGAL_OFFICER,
                                    User::ROLE_SYS_ADMIN,
                                ]
                            )
                    ),
            ],

            'responsible_officer_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'legal_notes' => [
                'nullable',
                'string',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'file' => DocumentUploadPolicy::rules(),
        ];
    }
}
