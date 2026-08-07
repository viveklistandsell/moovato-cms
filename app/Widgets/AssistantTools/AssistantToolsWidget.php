<?php

declare(strict_types=1);

namespace App\Widgets\AssistantTools;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Moovato assistant + tools: a soft assistant card (avatar, two selects and a
 * start button) above a "further articles & tools" heading with two link
 * columns. The avatar image is a setting; all copy is translatable data.
 */
final class AssistantToolsWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'assistant_tools';
    }

    public static function label(): string
    {
        return 'Assistant & Tools';
    }

    public static function icon(): string
    {
        return 'Bot';
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
            'avatar_path' => null,
            'avatar_url' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'assistant_title' => 'Moovato-Assistent',
            'assistant_subtitle' => 'Suchen Sie etwas? Wir sind hier, um zu helfen!',
            'avatar_alt' => 'Moovato Assistent',
            'selects' => [
                ['label' => 'Ich ziehe um', 'options' => ['International', 'Deutschlandweit', 'Innerhalb der Stadt']],
                ['label' => 'Suchen nach', 'options' => ['Umzugsangebote', 'Umzugskosten', 'Umzugsfirmen']],
            ],
            'button_label' => 'Start',
            'button_url' => '#',
            'tools_title' => 'Weitere Artikel und Hilfstools',
            'columns' => [
                [
                    'icon' => 'Truck',
                    'title' => 'Umzug Deutschlandweit',
                    'links' => [
                        ['label' => 'Umzug Deutschlandweit', 'url' => '#'],
                        ['label' => 'Umzugskosten 2026', 'url' => '#'],
                        ['label' => 'Umzugskosten berechnen', 'url' => '#'],
                    ],
                ],
                [
                    'icon' => 'Globe',
                    'title' => 'Auslandsumzug',
                    'links' => [
                        ['label' => 'Internationaler Umzug', 'url' => '#'],
                        ['label' => 'Internationale Umzüge Kosten 2026', 'url' => '#'],
                        ['label' => 'Umzug Europaweit', 'url' => '#'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'avatar_path' => ['nullable', 'string'],
            'avatar_url' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'assistant_title' => ['nullable', 'string'],
            'assistant_subtitle' => ['nullable', 'string'],
            'avatar_alt' => ['nullable', 'string'],
            'selects' => ['nullable', 'array'],
            'selects.*.label' => ['nullable', 'string'],
            'selects.*.options' => ['nullable', 'array'],
            'selects.*.options.*' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
            'tools_title' => ['nullable', 'string'],
            'columns' => ['nullable', 'array'],
            'columns.*.icon' => ['nullable', 'string'],
            'columns.*.title' => ['nullable', 'string'],
            'columns.*.links' => ['nullable', 'array'],
            'columns.*.links.*.label' => ['nullable', 'string'],
            'columns.*.links.*.url' => ['nullable', 'string'],
        ];
    }
}
