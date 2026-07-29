<?php

declare(strict_types=1);

use App\Widgets\CostCalculator\CostCalculatorWidget;
use App\Widgets\Registry\WidgetRegistry;

test('cost calculator widget is registered under the cost_calculator slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('cost_calculator'))->toBeTrue()
        ->and($registry->resolve('cost_calculator'))->toBe(CostCalculatorWidget::class);
});

test('cost calculator ships a pricing formula and geocoded locations', function (): void {
    $settings = CostCalculatorWidget::defaultSettings();

    expect($settings)->toHaveKeys([
        'currency',
        'base_price',
        'price_per_sqm',
        'price_per_km',
        'spread_percent',
        'locations',
    ])
        ->and($settings['locations'][0])->toHaveKeys(['name', 'lat', 'lng'])
        ->and($settings['locations'][0]['name'])->toBe('Berlin, Deutschland')
        ->and($settings['price_per_km'])->toBeGreaterThan(0);
});

test('the estimate is driven by distance and living space alone', function (): void {
    expect(CostCalculatorWidget::defaultSettings()['base_price'])->toBe(0);
});

test('the default rates match the reference calculator', function (): void {
    $settings = CostCalculatorWidget::defaultSettings();

    $total = $settings['base_price']
        + 82 * $settings['price_per_sqm']
        + 160 * $settings['price_per_km'];

    expect(round($total, 2))->toBe(899.20);
});

test('cost calculator ships german copy for the form and the post-calculation result', function (): void {
    $data = CostCalculatorWidget::defaultData();

    expect($data)->toHaveKeys([
        'title',
        'from_label',
        'to_label',
        'area_label',
        'calculate_label',
        'recalculate_label',
        'result_title',
        'volume_label',
        'distance_label',
        'empty_text',
        'error_text',
        'cta_text',
        'cta_label',
        'cta_url',
    ])
        ->and($data['title'])->toBe('Umzugskostenrechner')
        ->and($data['result_title'])->toBe('Geschätzte Kosten')
        ->and($data['recalculate_label'])->toBe('Erneut berechnen')
        ->and($data['cta_text'])->toContain('<p>');
});

test('cost calculator validates pricing settings and copy', function (): void {
    expect(CostCalculatorWidget::settingsRules())->toHaveKeys([
        'currency',
        'base_price',
        'price_per_sqm',
        'price_per_km',
        'spread_percent',
        'locations',
        'locations.*.name',
        'locations.*.lat',
        'locations.*.lng',
    ])
        ->and(CostCalculatorWidget::dataRules())->toHaveKeys([
            'title',
            'from_label',
            'to_label',
            'area_label',
            'calculate_label',
            'recalculate_label',
            'result_title',
            'empty_text',
            'error_text',
            'cta_text',
            'cta_label',
            'cta_url',
        ]);
});

test('cost calculator exposes the picker metadata shape', function (): void {
    $meta = CostCalculatorWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('cost_calculator')
        ->and($meta['label'])->toBe('Cost Calculator')
        ->and($meta['icon'])->toBe('Calculator')
        ->and($meta['category'])->toBe('marketing');
});
