<?php

return [
    'google_calendar' => [
        'client_id' => env('GOOGLE_CALENDAR_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CALENDAR_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_CALENDAR_REDIRECT_URI'),
        'refresh_token' => env('GOOGLE_CALENDAR_REFRESH_TOKEN'),
        'calendar_id' => env('GOOGLE_CALENDAR_ID', 'primary'),
        'timezone' => env('GOOGLE_CALENDAR_TIMEZONE', 'Asia/Singapore'),
    ],

    'ai_assist' => [
        'provider' => env('AI_ASSIST_PROVIDER', 'none'),
        'api_key' => env('AI_ASSIST_API_KEY'),
        'model' => env('AI_ASSIST_MODEL'),
        'endpoint' => env('AI_ASSIST_ENDPOINT'),
    ],
];
