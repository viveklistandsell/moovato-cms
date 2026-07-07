<?php

declare(strict_types=1);

use App\Widgets\Registry\WidgetRegistry;
use App\Widgets\TestimonialsShowcase\TestimonialsShowcaseWidget;

test('testimonials showcase is registered under its type slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('testimonials_showcase'))->toBeTrue()
        ->and($registry->resolve('testimonials_showcase'))->toBe(TestimonialsShowcaseWidget::class);
});

test('testimonials showcase keeps a distinct type from the simple testimonial widget', function (): void {
    expect(TestimonialsShowcaseWidget::type())->toBe('testimonials_showcase')
        ->and(TestimonialsShowcaseWidget::type())->not->toBe('testimonial')
        ->and(TestimonialsShowcaseWidget::category())->toBe('marketing');
});

test('testimonials showcase ships German reviews with ratings and identity', function (): void {
    $data = TestimonialsShowcaseWidget::defaultData();

    expect($data)->toHaveKeys(['eyebrow', 'heading', 'button_label', 'button_url', 'items'])
        ->and($data['items'])->toHaveCount(5)
        ->and($data['items'][0])->toHaveKeys(['rating', 'rating_text', 'quote', 'name', 'role', 'image_path', 'image_url'])
        ->and($data['items'][0]['rating'])->toBe(5)
        ->and($data['items'][0]['role'])->toContain('Berlin');
});

test('testimonials showcase validates ratings and per-item images', function (): void {
    $rules = TestimonialsShowcaseWidget::dataRules();

    expect($rules)->toHaveKeys([
        'items.*.rating',
        'items.*.quote',
        'items.*.name',
        'items.*.image_url',
    ])
        ->and($rules['items.*.rating'])->toBe(['nullable', 'integer', 'min:1', 'max:5']);
});

test('testimonials showcase exposes the picker metadata shape', function (): void {
    $meta = TestimonialsShowcaseWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('testimonials_showcase')
        ->and($meta['label'])->toBe('Testimonials Showcase');
});
