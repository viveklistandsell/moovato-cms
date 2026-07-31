<?php

declare(strict_types=1);

use App\Widgets\CitySearch\CitySearchWidget;
use App\Widgets\Registry\WidgetRegistry;

test('city search widget is registered under the city_search slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('city_search'))->toBeTrue()
        ->and($registry->resolve('city_search'))->toBe(CitySearchWidget::class);
});

test('city search ships city image cards and german directory copy', function (): void {
    $settings = CitySearchWidget::defaultSettings();
    $data = CitySearchWidget::defaultData();

    expect($settings)->toHaveKey('cities')
        ->and($settings['cities'][0])->toHaveKeys(['path', 'url', 'alt', 'name', 'link'])
        ->and($data)->toHaveKeys([
            'title',
            'subtitle',
            'card_prefix',
            'search_placeholder',
            'search_url',
        ])
        ->and($data['title'])->toContain('Umzugsunternehmen');
});

test('city search validates its settings and data fields', function (): void {
    expect(CitySearchWidget::settingsRules())->toHaveKeys([
        'cities',
        'cities.*.path',
        'cities.*.url',
        'cities.*.name',
        'cities.*.link',
    ])
        ->and(CitySearchWidget::dataRules())->toHaveKeys([
            'title',
            'subtitle',
            'card_prefix',
            'search_placeholder',
            'search_url',
        ]);
});

test('city search exposes the picker metadata shape', function (): void {
    $meta = CitySearchWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('city_search')
        ->and($meta['label'])->toBe('City Directory Search')
        ->and($meta['category'])->toBe('content');
});
