<?php

declare(strict_types=1);

use App\Widgets\CompanyTopList\CompanyTopListWidget;
use App\Widgets\Registry\WidgetRegistry;

test('company top list widget is registered under the company_top_list slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('company_top_list'))->toBeTrue()
        ->and($registry->resolve('company_top_list'))->toBe(CompanyTopListWidget::class);
});

test('company top list references companies by id rather than copied fields', function (): void {
    $settings = CompanyTopListWidget::defaultSettings();

    expect($settings)->toHaveKeys([
        'mode',
        'company_ids',
        'limit',
        'only_verified',
        'show_filters',
        'show_sort',
    ])
        ->and($settings['mode'])->toBe('auto')
        ->and($settings['company_ids'])->toBe([])
        ->and($settings['limit'])->toBe(10)
        ->and($settings['show_filters'])->toBeTrue()
        ->and($settings['show_sort'])->toBeTrue();
});

test('company top list ships filter and sort copy with placeholders', function (): void {
    $data = CompanyTopListWidget::defaultData();

    expect($data)->toHaveKeys([
        'count_text',
        'filters_title',
        'results_title',
        'services_title',
        'rating_title',
        'features_title',
        'nearby_title',
        'nearby_label',
        'reset_label',
        'no_results_text',
        'sort_label',
        'sort_relevance_label',
        'sort_rating_label',
        'sort_reviews_label',
        'sort_name_label',
    ])
        ->and($data['count_text'])->toContain('{count}')
        ->and($data['nearby_label'])->toContain('{city}')
        ->and($data['filters_title'])->toBe('Filter')
        ->and($data['results_title'])->toBe('Ergebnisse');
});

test('company top list ships german card copy', function (): void {
    $data = CompanyTopListWidget::defaultData();

    expect($data)->toHaveKeys([
        'heading',
        'subheading',
        'badge_label',
        'reviews_label',
        'recommend_label',
        'quote_label',
        'quote_url',
        'details_label',
        'empty_text',
    ])
        ->and($data['heading'])->toBe('Top 10 Umzugsunternehmen in Berlin')
        ->and($data['quote_label'])->toBe('Angebot anfordern')
        ->and($data['details_label'])->toBe('Details');
});

test('company top list validates the picker settings and copy', function (): void {
    expect(CompanyTopListWidget::settingsRules())->toHaveKeys([
        'mode',
        'company_ids',
        'company_ids.*',
        'limit',
        'only_verified',
    ])
        ->and(CompanyTopListWidget::dataRules())->toHaveKeys([
            'heading',
            'subheading',
            'badge_label',
            'quote_label',
            'quote_url',
            'details_label',
            'empty_text',
        ]);
});

test('company top list exposes the picker metadata shape', function (): void {
    $meta = CompanyTopListWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('company_top_list')
        ->and($meta['label'])->toBe('Company Top List')
        ->and($meta['icon'])->toBe('Trophy')
        ->and($meta['category'])->toBe('marketing');
});
