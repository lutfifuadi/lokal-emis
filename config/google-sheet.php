<?php

return [
    'credentials_path' => storage_path('app/google-sheet/credentials.json'),

    'application_name' => env('GOOGLE_SHEET_APP_NAME', 'Aplikasi Lokal EMIS'),

    'scopes' => [
        Google_Service_Sheets::SPREADSHEETS_READONLY,
        Google_Service_Sheets::SPREADSHEETS,
    ],

    'auth_config' => [
        'subject' => env('GOOGLE_SHEET_SUBJECT'),
    ],
];
