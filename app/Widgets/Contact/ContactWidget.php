<?php

declare(strict_types=1);

namespace App\Widgets\Contact;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Contact section (redesigned from the Amoxi "contact-one" template and
 * recolored to the theme): a left column with three info cards (phone / email /
 * location) above an embedded Google map, paired with a right column holding an
 * eyebrow, two-tone heading, optional rich-text lead and a Name / Email / Phone
 * / Service / Message form that resolves to a success state. A large watermark
 * word sits behind the bottom edge. Surrounding copy + the service list are
 * editable; the form fields themselves are fixed.
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
    public static function defaultSettings(): array
    {
        return [
            'map_embed_url' => 'https://www.google.com/maps?q=Berlin,+Deutschland&output=embed',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'phone_title' => 'Rufen Sie uns an',
            'phone' => '+49 30 1234 5678',
            'email_title' => 'E-Mail Adresse',
            'email' => 'hallo@moovato.de',
            'location_title' => 'Unser Standort',
            'address' => 'Musterstraße 12, 10115 Berlin',
            'address_url' => 'https://www.google.com/maps?q=Berlin,+Deutschland',
            'eyebrow' => 'Kontakt aufnehmen',
            'heading_lead' => 'Fordern Sie jetzt ein',
            'heading_highlight' => 'kostenloses Angebot an.',
            'description' => '<p>Erzählen Sie uns kurz von Ihrem Umzug in Berlin – wir melden uns innerhalb von 24 Stunden mit Ihrem persönlichen Festpreis-Angebot.</p>',
            'name_placeholder' => 'Ihr Name *',
            'email_placeholder' => 'Ihre E-Mail *',
            'phone_placeholder' => 'Ihre Telefonnummer *',
            'service_placeholder' => 'Service auswählen',
            'services' => [
                'Privat',
                'Gewerbe',
                'Fernumzug',
                'Spezialtransport',
            ],
            'message_placeholder' => 'Ihre Nachricht *',
            'submit_label' => 'Anfrage senden',
            'success_title' => 'Vielen Dank!',
            'success_text' => 'Ihre Anfrage ist bei uns eingegangen. Wir melden uns innerhalb von 24 Stunden bei Ihnen.',
            'watermark' => 'KONTAKT',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'map_embed_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'phone_title' => ['nullable', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:80'],
            'email_title' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'string', 'max:160'],
            'location_title' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_url' => ['nullable', 'string', 'max:2048'],
            'eyebrow' => ['nullable', 'string', 'max:80'],
            'heading_lead' => ['nullable', 'string', 'max:120'],
            'heading_highlight' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'name_placeholder' => ['nullable', 'string', 'max:80'],
            'email_placeholder' => ['nullable', 'string', 'max:80'],
            'phone_placeholder' => ['nullable', 'string', 'max:80'],
            'service_placeholder' => ['nullable', 'string', 'max:80'],
            'services' => ['nullable', 'array', 'max:12'],
            'services.*' => ['nullable', 'string', 'max:80'],
            'message_placeholder' => ['nullable', 'string', 'max:160'],
            'submit_label' => ['nullable', 'string', 'max:60'],
            'success_title' => ['nullable', 'string', 'max:120'],
            'success_text' => ['nullable', 'string', 'max:500'],
            'watermark' => ['nullable', 'string', 'max:40'],
        ];
    }
}
