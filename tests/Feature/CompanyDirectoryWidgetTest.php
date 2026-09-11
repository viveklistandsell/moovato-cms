<?php

declare(strict_types=1);

use App\Widgets\CompanyDirectory\CompanyDirectoryWidget;
use App\Widgets\Registry\WidgetRegistry;

test('company directory widget is registered under the company_directory slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('company_directory'))->toBeTrue()
        ->and($registry->resolve('company_directory'))->toBe(CompanyDirectoryWidget::class);
});

test('company directory ships German Moovato companies with scores and pros/cons in settings', function (): void {
    $settings = CompanyDirectoryWidget::defaultSettings();

    expect($settings)->toHaveKey('companies')
        ->and($settings['companies'])->not->toBeEmpty()
        ->and($settings['companies'][0])->toHaveKeys([
            'name', 'score', 'reviews_count', 'pro_reviews_count', 'services', 'pros', 'cons', 'quote_url',
        ])
        ->and($settings['companies'][0]['name'])->toBe('Moovato Express Umzüge')
        ->and($settings['companies'][0]['ribbon_label'])->not->toBeEmpty();
});

test('company directory exposes translatable headings and button labels', function (): void {
    $data = CompanyDirectoryWidget::defaultData();

    expect($data)->toHaveKeys([
        'heading', 'subheading', 'all_label', 'score_label', 'pro_reviews_label',
        'rating_prefix', 'pros_heading', 'cons_heading', 'quote_label',
    ])
        ->and($data['quote_label'])->toBe('Angebot anfordern');
});

test('company directory validates the new pros/cons and ribbon fields', function (): void {
    $rules = CompanyDirectoryWidget::settingsRules();

    expect($rules)->toHaveKeys([
        'companies.*.ribbon_label',
        'companies.*.pro_reviews_count',
        'companies.*.pros',
        'companies.*.cons',
    ]);
});

test('company directory exposes the picker metadata shape', function (): void {
    $meta = CompanyDirectoryWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('company_directory')
        ->and($meta['category'])->toBe('marketing');
});
