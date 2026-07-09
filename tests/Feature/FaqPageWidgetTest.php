<?php

declare(strict_types=1);

use App\Widgets\FaqPage\FaqPageWidget;
use App\Widgets\Registry\WidgetRegistry;

test('faq page widget is registered under its type slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('faq_page'))->toBeTrue()
        ->and($registry->resolve('faq_page'))->toBe(FaqPageWidget::class);
});

test('faq page keeps a distinct type from the other faq widgets', function (): void {
    expect(FaqPageWidget::type())->toBe('faq_page')
        ->and(FaqPageWidget::type())->not->toBe('faq')
        ->and(FaqPageWidget::type())->not->toBe('faq_media');
});

test('faq page ships search toggles and categorised German questions', function (): void {
    $settings = FaqPageWidget::defaultSettings();
    $data = FaqPageWidget::defaultData();

    expect($settings)->toHaveKeys(['show_search', 'show_categories', 'first_open'])
        ->and($settings['show_search'])->toBeTrue()
        ->and($data)->toHaveKeys(['eyebrow', 'heading', 'categories'])
        ->and($data['categories'])->toHaveCount(4)
        ->and($data['categories'][0])->toHaveKeys(['name', 'items'])
        ->and($data['categories'][0]['items'][0])->toHaveKeys(['question', 'answer'])
        ->and($data['categories'][0]['items'][0]['answer'])->toContain('<p>');
});

test('faq page exposes the picker metadata shape', function (): void {
    $meta = FaqPageWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('faq_page')
        ->and($meta['category'])->toBe('content');
});
