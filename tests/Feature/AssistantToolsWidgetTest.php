<?php

declare(strict_types=1);

use App\Widgets\AssistantTools\AssistantToolsWidget;
use App\Widgets\Registry\WidgetRegistry;

test('assistant tools widget is registered under the assistant_tools slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('assistant_tools'))->toBeTrue()
        ->and($registry->resolve('assistant_tools'))->toBe(AssistantToolsWidget::class);
});

test('assistant tools ships selects, link columns and german copy', function (): void {
    $settings = AssistantToolsWidget::defaultSettings();
    $data = AssistantToolsWidget::defaultData();

    expect($settings)->toHaveKeys(['avatar_path', 'avatar_url'])
        ->and($data)->toHaveKeys([
            'assistant_title',
            'assistant_subtitle',
            'selects',
            'button_label',
            'tools_title',
            'columns',
        ])
        ->and($data['selects'][0])->toHaveKeys(['label', 'options'])
        ->and($data['columns'][0])->toHaveKeys(['icon', 'title', 'links'])
        ->and($data['columns'][0]['links'][0])->toHaveKeys(['label', 'url'])
        ->and($data['tools_title'])->toBe('Weitere Artikel und Hilfstools');
});

test('assistant tools validates nested selects and columns', function (): void {
    expect(AssistantToolsWidget::settingsRules())->toHaveKeys(['avatar_path', 'avatar_url'])
        ->and(AssistantToolsWidget::dataRules())->toHaveKeys([
            'assistant_title',
            'assistant_subtitle',
            'selects',
            'selects.*.label',
            'selects.*.options',
            'selects.*.options.*',
            'button_label',
            'button_url',
            'tools_title',
            'columns',
            'columns.*.icon',
            'columns.*.title',
            'columns.*.links',
            'columns.*.links.*.label',
            'columns.*.links.*.url',
        ]);
});

test('assistant tools exposes the picker metadata shape', function (): void {
    $meta = AssistantToolsWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('assistant_tools')
        ->and($meta['label'])->toBe('Assistant & Tools')
        ->and($meta['category'])->toBe('content');
});
