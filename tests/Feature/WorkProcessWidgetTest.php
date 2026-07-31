<?php

declare(strict_types=1);

use App\Widgets\Registry\WidgetRegistry;
use App\Widgets\WorkProcess\WorkProcessWidget;

test('work process widget is registered under its type slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('work_process'))->toBeTrue()
        ->and($registry->resolve('work_process'))->toBe(WorkProcessWidget::class);
});

test('work process ships numbered German steps with image slots', function (): void {
    $data = WorkProcessWidget::defaultData();

    expect($data)->toHaveKeys(['eyebrow', 'heading', 'description', 'step_label', 'button_label', 'items'])
        ->and($data['step_label'])->toBe('Schritt')
        ->and($data['items'])->toHaveCount(4)
        ->and($data['items'][0])->toHaveKeys(['title', 'url', 'image_alt', 'image_path', 'image_url']);
});

test('work process exposes the picker metadata shape', function (): void {
    $meta = WorkProcessWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('work_process')
        ->and($meta['label'])->toBe('Work Process')
        ->and($meta['category'])->toBe('content');
});
