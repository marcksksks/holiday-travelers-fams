<?php

namespace App\Support;

use Illuminate\Validation\Rules\File;

final class DocumentUploadPolicy
{
    public const MAX_SIZE_KB = 20480;

    public const ALLOWED_EXTENSIONS = [
        'pdf',
        'docx',
        'xlsx',
        'pptx',
        'txt',
        'csv',
        'jpg',
        'jpeg',
        'png',
    ];

    public static function rules(
        bool $required = false
    ): array {
        return [
            $required
                ? 'required'
                : 'nullable',

            'file',

            File::types(
                self::ALLOWED_EXTENSIONS
            )->max(
                self::MAX_SIZE_KB
            ),

            'extensions:'
                .implode(
                    ',',
                    self::ALLOWED_EXTENSIONS
                ),
        ];
    }

    public static function acceptAttribute(): string
    {
        return implode(
            ',',
            array_map(
                static fn (string $extension): string =>
                    ".{$extension}",
                self::ALLOWED_EXTENSIONS
            )
        );
    }
}