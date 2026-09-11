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

    'notifications' => [
        'plan_updated_title' => 'Your :plan plan has been updated',
        'plan_updated_body' => 'Our team has adjusted your plan. See the changes that now apply below.',
    ],

    'reviews' => [
        'errors' => [
            'reply_cap_reached' => 'You have reached your plan\'s reply limit. Upgrade your plan to reply to more reviews.',
        ],
        'flash' => [
            'reply_saved' => 'Reply saved.',
            'reply_deleted' => 'Reply removed.',
            'hidden' => 'Review hidden.',
            'unhidden' => 'Review is now visible again.',
            'spam_marked' => 'Review marked as spam.',
            'spam_unmarked' => 'Review is no longer marked as spam.',
            'review_deleted' => 'Review deleted.',
        ],
    ],

    'plans' => [
        'already_on' => 'You are already on the :tier plan.',
        'upgraded' => 'You are now on the :tier plan — new features are already active.',
        'downgraded' => 'Your plan has been changed to :tier. Content beyond the new limits has been trimmed.',
        'no_company' => 'No company linked to your account — please contact support.',
        'request_submitted' => 'Request to change to the :tier plan sent. Our team will review it shortly.',
        'request_already_pending' => 'You already have a plan-change request pending. Please cancel it first before submitting a new one.',
        'request_not_pending' => 'This request has already been processed.',
        'request_cancelled' => 'Your plan-change request has been cancelled.',
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
