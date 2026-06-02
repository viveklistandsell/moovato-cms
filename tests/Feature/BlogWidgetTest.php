<?php

declare(strict_types=1);

use App\Widgets\Blog\BlogWidget;
use App\Widgets\Registry\WidgetRegistry;

test('blog widget is registered under the blog slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('blog'))->toBeTrue()
        ->and($registry->resolve('blog'))->toBe(BlogWidget::class);
});

test('blog ships count, columns and category settings with german heading', function (): void {
    $settings = BlogWidget::defaultSettings();
    $data = BlogWidget::defaultData();

    expect($settings)->toHaveKeys(['count', 'columns', 'category'])
        ->and($settings['count'])->toBe(3)
        ->and($settings['columns'])->toBe(3)
        ->and($data['eyebrow'])->toBe('News & Blog')
        ->and($data)->toHaveKeys(['heading', 'read_more_label', 'cta_label', 'cta_url']);
});

test('blog validates count bounds and column choices', function (): void {
    $rules = BlogWidget::settingsRules();

    expect($rules['count'])->toContain('integer', 'min:1', 'max:12')
        ->and($rules['columns'])->toContain('in:2,3,4');
});

test('blog exposes the picker metadata shape', function (): void {
    $meta = BlogWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('blog')
        ->and($meta['category'])->toBe('content');
});
