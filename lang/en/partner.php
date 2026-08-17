<?php

declare(strict_types=1);

return [
    'register' => [
        'errors' => [
            'accept_terms' => 'You must accept the terms and conditions to register.',
            'email_unique' => 'An account with this email already exists. Try logging in instead.',
            'spam_rejected' => 'Your submission was flagged as spam and rejected.',
            'captcha_required' => 'Please confirm you are not a robot.',
            'captcha_invalid' => 'The reCAPTCHA verification failed. Please try again.',
        ],
    ],

    'auth' => [
        'invalid_credentials' => 'The email or password you entered is incorrect.',
        'status_pending' => 'Your application is still under review. You will receive an email once it is approved.',
        'status_rejected' => 'Your application was rejected. Please contact support if you believe this is a mistake.',
        'status_inactive' => 'This account is inactive. Please contact support to reactivate it.',
        'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
        'forgot_sent' => 'If an approved partner account exists for that email, we sent you a reset link.',
        'password_updated' => 'Your password has been updated. Please sign in.',
        'token_invalid' => 'This reset link is invalid or has expired. Please request a new one.',
        'reset_throttled' => 'You are requesting resets too fast. Please wait a moment and try again.',
        'reset_failed' => 'We could not reset your password. Please try again.',
    ],
];
