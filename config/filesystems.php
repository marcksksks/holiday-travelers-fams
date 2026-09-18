<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        'documents' => [
            // Private, confidentiality-gated storage for ArchiveDocument / Contract / LegalRecord files.
            'driver' => 'local',
            'root' => storage_path('app/documents'),
            'serve' => false,
            'throw' => false,
            'report' => true,
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
