<?php

return [
    'recipient' => [
        'email' => env('CONTACT_RECIPIENT_EMAIL', env('MAIL_FROM_ADDRESS', 'contato@example.com')),
        'name' => env('CONTACT_RECIPIENT_NAME', env('APP_NAME', 'Institucional Base')),
    ],
];