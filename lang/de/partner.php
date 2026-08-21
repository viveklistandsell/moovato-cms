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

    'notifications' => [
        'plan_updated_title' => 'Ihr :plan-Plan wurde aktualisiert',
        'plan_updated_body' => 'Unser Team hat Ihren Plan angepasst. Die neuen Werte sind unten aufgelistet.',
    ],

    'reviews' => [
        'errors' => [
            'reply_cap_reached' => 'Sie haben das Antwort-Limit Ihres Plans erreicht. Upgraden Sie Ihren Plan, um weitere Antworten zu verfassen.',
        ],
        'flash' => [
            'reply_saved' => 'Antwort gespeichert.',
            'reply_deleted' => 'Antwort entfernt.',
            'hidden' => 'Bewertung ausgeblendet.',
            'unhidden' => 'Bewertung wird wieder angezeigt.',
            'spam_marked' => 'Bewertung als Spam markiert.',
            'spam_unmarked' => 'Spam-Markierung entfernt.',
            'review_deleted' => 'Bewertung gelöscht.',
        ],
    ],

    'plans' => [
        'already_on' => 'Sie sind bereits auf dem :tier-Plan.',
        'upgraded' => 'Sie sind jetzt auf dem :tier-Plan — die neuen Funktionen sind bereits aktiv.',
        'downgraded' => 'Ihr Plan wurde auf :tier geändert. Inhalte über den neuen Limits wurden angepasst.',
        'no_company' => 'Kein Unternehmen mit Ihrem Konto verknüpft — bitte kontaktieren Sie den Support.',
        'request_submitted' => 'Anfrage für den :tier-Plan wurde gesendet. Unser Team prüft sie zeitnah.',
        'request_already_pending' => 'Sie haben bereits eine offene Plan-Anfrage. Bitte ziehen Sie diese zuerst zurück, bevor Sie eine neue senden.',
        'request_not_pending' => 'Diese Anfrage wurde bereits bearbeitet.',
        'request_cancelled' => 'Ihre Plan-Anfrage wurde zurückgezogen.',
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
