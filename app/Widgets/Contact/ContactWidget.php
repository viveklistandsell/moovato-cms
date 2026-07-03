<?php

declare(strict_types=1);

namespace App\Widgets\Contact;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Contact section: a dark card on the left with an eyebrow, a two-tone heading
 * and two info blocks (email/phone + address), paired with a Name/Email/Message
 * contact form on the right that resolves to a success state. All surrounding
 * copy is editable; the form fields themselves are fixed.
 */
final class ContactWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'contact';
    }

    public static function label(): string
    {
        return 'Contact';
    }

    public static function icon(): string
    {
        return 'Mail';
    }

    public static function category(): string
    {
        return 'content';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Kontakt',
            'heading_lead' => 'Lassen Sie uns Ihren',
            'heading_highlight' => 'Umzug planen.',
            'email_label' => 'Schreiben Sie uns',
            'email' => 'hallo@moovato.de',
            'phone' => '+49 30 1234 5678',
            'location_label' => 'Standort',
            'address' => 'Moovato Umzüge, Musterstraße 12, 10115 Berlin',
            'name_field_label' => 'Name',
            'email_field_label' => 'E-Mail',
            'message_field_label' => 'Nachricht',
            'message_placeholder' => 'Erzählen Sie uns kurz von Ihrem Umzug',
            'submit_label' => 'Jetzt senden',
            'success_title' => 'Vielen Dank!',
            'success_text' => 'Ihre Nachricht ist bei uns eingegangen. Wir melden uns innerhalb von 24 Stunden bei Ihnen.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:80'],
            'heading_lead' => ['nullable', 'string', 'max:120'],
            'heading_highlight' => ['nullable', 'string', 'max:120'],
            'email_label' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:80'],
            'location_label' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:255'],
            'name_field_label' => ['nullable', 'string', 'max:60'],
            'email_field_label' => ['nullable', 'string', 'max:60'],
            'message_field_label' => ['nullable', 'string', 'max:60'],
            'message_placeholder' => ['nullable', 'string', 'max:160'],
            'submit_label' => ['nullable', 'string', 'max:60'],
            'success_title' => ['nullable', 'string', 'max:120'],
            'success_text' => ['nullable', 'string', 'max:500'],
        ];
    }
}
