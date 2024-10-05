<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],
    'api' => [
        'host' => env('WEATHER_API_URL'),
        'secret' => env('WEATHER_API_KEY'),
    ]
];
