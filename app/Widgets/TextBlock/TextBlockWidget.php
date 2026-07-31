<?php

declare(strict_types=1);

namespace App\Widgets\TextBlock;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class TextBlockWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'text_block';
    }

    public static function label(): string
    {
        return 'Text Block';
    }

    public static function icon(): string
    {
        return 'Type';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'width' => 'narrow',  // narrow | wide | full
            'alignment' => 'left', // left | center | right
            'background' => 'none', // none | muted | brand
        ];
    }

    public static function defaultData(): array
    {
        return [
            'heading' => 'Ihr Umzugsunternehmen in Berlin',
            'body' => '<p>Moovato ist Ihr moderner Partner für stressfreie Umzüge in Berlin und Umgebung. Von Privatumzug über Gewerbeumzug und Fernumzug bis hin zum Spezialtransport kümmern wir uns um alles: vom sorgfältigen Verpacken über den sicheren Transport bis zur Übergabe am neuen Ort.</p><p>Profitieren Sie von unserer <strong>Festpreisgarantie</strong>, versicherten Transporten und einem eingespielten Team erfahrener Umzugsprofis.</p>',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'width' => ['required', 'in:narrow,wide,full'],
            'alignment' => ['required', 'in:left,center,right'],
            'background' => ['required', 'in:none,muted,brand'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
        ];
    }
}
