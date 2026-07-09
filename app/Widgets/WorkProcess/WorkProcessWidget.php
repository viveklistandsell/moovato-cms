<?php

declare(strict_types=1);

namespace App\Widgets\WorkProcess;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Work Process: a dark section with an intro (eyebrow + heading + description)
 * and a staggered row of numbered steps — each a "Step 0X" pill, a dashed
 * connector of increasing height, a title link and a rounded image.
 */
final class WorkProcessWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'work_process';
    }

    public static function label(): string
    {
        return 'Work Process';
    }

    public static function icon(): string
    {
        return 'Workflow';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'bg_image_path' => null,
            'bg_image_url' => null,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Unser Ablauf',
            'heading' => 'So läuft Ihr Umzug ab.',
            'description' => 'Von der ersten Anfrage bis zur Möbelmontage begleiten wir Sie in klaren Schritten – transparent, planbar und mit Festpreisgarantie.',
            'step_label' => 'Schritt',
            'button_label' => 'Alle Leistungen',
            'button_url' => '/leistungen',
            'items' => [
                ['title' => 'Anfrage & Beratung', 'url' => '#anfrage', 'icon' => 'PhoneCall', 'image_alt' => 'Kostenlose Umzugsberatung', 'image_path' => null, 'image_url' => null],
                ['title' => 'Festpreis-Angebot', 'url' => '#angebot', 'icon' => 'FileText', 'image_alt' => 'Unverbindliches Festpreis-Angebot', 'image_path' => null, 'image_url' => null],
                ['title' => 'Planung & Termin', 'url' => '#planung', 'icon' => 'CalendarCheck', 'image_alt' => 'Umzugsplanung und Terminvergabe', 'image_path' => null, 'image_url' => null],
                ['title' => 'Umzugstag & Montage', 'url' => '#umzug', 'icon' => 'Truck', 'image_alt' => 'Umzugstag mit dem Moovato Team', 'image_path' => null, 'image_url' => null],
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'bg_image_path' => ['nullable', 'string', 'max:1000'],
            'bg_image_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:600'],
            'step_label' => ['nullable', 'string', 'max:40'],
            'button_label' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:2048'],
            'items' => ['nullable', 'array', 'max:8'],
            'items.*.title' => ['nullable', 'string', 'max:160'],
            'items.*.url' => ['nullable', 'string', 'max:2048'],
            'items.*.icon' => ['nullable', 'string', 'max:80'],
            'items.*.image_alt' => ['nullable', 'string', 'max:160'],
            'items.*.image_path' => ['nullable', 'string', 'max:1000'],
            'items.*.image_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
