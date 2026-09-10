<?php

declare(strict_types=1);

namespace App\Widgets\ImageCard;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class ImageCardWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'image_card';
    }

    public static function label(): string
    {
        return 'Image Card (Read More)';
    }

    public static function icon(): string
    {
        return 'GalleryVerticalEnd';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'image_path' => null,
            'image_url' => null,
            'read_more_enabled' => false,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'heading' => 'Festpreis statt Stundenzettel',
            'body' => "Nach der kostenlosen Besichtigung bekommen Sie ein schriftliches Angebot, das jede Position einzeln ausweist: Personal, Fahrzeug, Packmaterial, Möbellift, Halteverbot sowie De- und Montage. Sie sehen genau, wofür Sie zahlen.\n\nWas dort steht, wird berechnet – auch wenn der Umzugstag länger dauert als gedacht. Nachträgliche Stundenzuschläge gibt es bei uns nicht, und ein Sofa, das im Treppenhaus klemmt, geht nicht zu Ihren Lasten.\n\nNur wenn Sie selbst den Umfang ändern, etwa weil der Keller doch mitkommt, passen wir das Angebot gemeinsam an. Diese Entscheidung treffen Sie – nicht der Vorarbeiter am Umzugstag.",
            'image_alt' => 'Glückliches Paar mit Umzugskartons in ihrer neuen Wohnung in Berlin',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'read_more_enabled' => ['boolean'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
        ];
    }
}
