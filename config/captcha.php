<?php

declare(strict_types=1);

return [

    'secret' => env('NOCAPTCHA_SECRET'),
    'sitekey' => env('NOCAPTCHA_SITEKEY'),

    /*
    |--------------------------------------------------------------------------
    | Guzzle client options passed to the package's HTTP client.
    |--------------------------------------------------------------------------
    |
    | `verify` controls whether Guzzle verifies Google's SSL certificate
    | when POSTing to https://www.google.com/recaptcha/api/siteverify.
    |
    | Toggle via the CAPTCHA_VERIFY_SSL env var. Default `true`; set it
    | to `false` in local .env if you get "unable to get local issuer
    | certificate".
    |
    */

    'options' => [
        'timeout' => 30,
        'verify' => env('CAPTCHA_VERIFY_SSL', true),
    ],
];
