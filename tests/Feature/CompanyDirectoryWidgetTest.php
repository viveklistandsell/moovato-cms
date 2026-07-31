<?php

declare(strict_types=1);

use App\Widgets\CompanyDirectory\CompanyDirectoryWidget;
use App\Widgets\Registry\WidgetRegistry;

test('company directory widget is registered under the company_directory slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('company_directory'))->toBeTrue()
        ->and($registry->resolve('company_directory'))->toBe(CompanyDirectoryWidget::class);
});

test('company directory ships German Moovato companies with scores in settings', function (): void {
    $settings = CompanyDirectoryWidget::defaultSettings();

    expect($settings)->toHaveKey('companies')
        ->and($settings['companies'])->not->toBeEmpty()
        ->and($settings['companies'][0])->toHaveKeys([
            'name', 'score', 'reviews_count', 'badges', 'services', 'request_url', 'quote_url',
        ])
        ->and($settings['companies'][0]['name'])->toBe('Moovato Express Umzüge')
        ->and($settings['companies'][0]['top_pro'])->toBeTrue();
});

test('company directory exposes translatable headings and button labels', function (): void {
    $data = CompanyDirectoryWidget::defaultData();

    expect($data)->toHaveKeys([
        'heading', 'subheading', 'all_label', 'score_label', 'request_label', 'quote_label',
    ])
        ->and($data['request_label'])->toBe('Verfügbarkeit anfragen')
        ->and($data['quote_label'])->toBe('Angebot einholen');
});

test('company directory exposes the picker metadata shape', function (): void {
    $meta = CompanyDirectoryWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('company_directory')
        ->and($meta['category'])->toBe('marketing');
});
