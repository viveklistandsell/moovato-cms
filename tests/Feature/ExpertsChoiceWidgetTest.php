<?php

declare(strict_types=1);

use App\Widgets\ExpertsChoice\ExpertsChoiceWidget;
use App\Widgets\Registry\WidgetRegistry;

test('experts choice widget is registered under the experts_choice slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('experts_choice'))->toBeTrue()
        ->and($registry->resolve('experts_choice'))->toBe(ExpertsChoiceWidget::class);
});

test('experts choice ships a dark card, rating ribbon and feature cards', function (): void {
    $data = ExpertsChoiceWidget::defaultData();

    expect($data)->toHaveKeys([
        'eyebrow',
        'heading',
        'body',
        'points',
        'button_label',
        'button_url',
        'since_label',
        'cards',
    ])
        ->and($data['points'])->toHaveCount(4)
        ->and($data['cards'])->toHaveCount(4)
        ->and($data['cards'][0])->toHaveKeys(['icon', 'title', 'description'])
        ->and($data['since_label'])->toBe('Seit 2014');
});

test('experts choice exposes the picker metadata shape', function (): void {
    $meta = ExpertsChoiceWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('experts_choice')
        ->and($meta['label'])->toBe('Experts Choice')
        ->and($meta['category'])->toBe('content');
});
