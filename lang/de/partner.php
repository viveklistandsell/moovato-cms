<?php

declare(strict_types=1);

return [
    'register' => [
        'errors' => [
            'accept_terms' => 'Sie müssen die AGB akzeptieren, um sich zu registrieren.',
            'email_unique' => 'Ein Konto mit dieser E-Mail-Adresse existiert bereits. Bitte melden Sie sich stattdessen an.',
            'spam_rejected' => 'Ihre Einreichung wurde als Spam erkannt und abgelehnt.',
            'captcha_required' => 'Bitte bestätigen Sie, dass Sie kein Roboter sind.',
            'captcha_invalid' => 'Die reCAPTCHA-Überprüfung ist fehlgeschlagen. Bitte versuchen Sie es erneut.',
        ],
    ],

    'auth' => [
        'invalid_credentials' => 'Die eingegebene E-Mail-Adresse oder das Passwort ist falsch.',
        'status_pending' => 'Ihre Anmeldung wird noch geprüft. Sie erhalten eine E-Mail, sobald sie freigegeben wurde.',
        'status_rejected' => 'Ihre Anmeldung wurde abgelehnt. Bitte kontaktieren Sie den Support, falls dies ein Fehler ist.',
        'status_inactive' => 'Dieses Konto ist inaktiv. Bitte kontaktieren Sie den Support, um es zu reaktivieren.',
        'throttle' => 'Zu viele Anmeldeversuche. Bitte versuchen Sie es in :seconds Sekunden erneut.',
        'forgot_sent' => 'Falls ein freigegebenes Partnerkonto mit dieser E-Mail existiert, haben wir Ihnen einen Link zum Zurücksetzen gesendet.',
        'password_updated' => 'Ihr Passwort wurde aktualisiert. Bitte melden Sie sich an.',
        'token_invalid' => 'Dieser Link ist ungültig oder abgelaufen. Bitte fordern Sie einen neuen an.',
        'reset_throttled' => 'Sie fordern zu schnell Zurücksetzungen an. Bitte warten Sie einen Moment und versuchen Sie es erneut.',
        'reset_failed' => 'Wir konnten Ihr Passwort nicht zurücksetzen. Bitte versuchen Sie es erneut.',
    ],
];
