<?php

declare(strict_types=1);

use App\Widgets\Comparison\ComparisonWidget;
use App\Widgets\Registry\WidgetRegistry;

test('comparison widget is registered under the comparison slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('comparison'))->toBeTrue()
        ->and($registry->resolve('comparison'))->toBe(ComparisonWidget::class);
});

test('comparison ships a price tab and a check/cross performance tab', function (): void {
    $data = ComparisonWidget::defaultData();

    expect($data)->toHaveKeys(['eyebrow', 'heading', 'tabs'])
        ->and($data['tabs'])->toHaveCount(2)
        ->and($data['tabs'][0]['mode'])->toBe('value')
        ->and($data['tabs'][1]['mode'])->toBe('check')
        ->and($data['tabs'][1]['rows'][0])->toMatchArray([
            'label' => 'Preistransparenz',
            'left' => false,
            'right' => true,
        ]);
});

test('comparison exposes the picker metadata shape', function (): void {
    $meta = ComparisonWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('comparison')
        ->and($meta['label'])->toBe('Comparison Table')
        ->and($meta['category'])->toBe('marketing');
});
