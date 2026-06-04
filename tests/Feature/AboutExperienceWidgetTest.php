<?php

declare(strict_types=1);

use App\Widgets\AboutExperience\AboutExperienceWidget;
use App\Widgets\Registry\WidgetRegistry;

test('about experience widget is registered under the about_experience slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('about_experience'))->toBeTrue()
        ->and($registry->resolve('about_experience'))->toBe(AboutExperienceWidget::class);
});

test('about experience ships image slots, founder and checklist', function (): void {
    $settings = AboutExperienceWidget::defaultSettings();
    $data = AboutExperienceWidget::defaultData();

    expect($settings)->toHaveKeys([
        'image_path',
        'image_url',
        'image_2_path',
        'image_2_url',
        'founder_path',
        'founder_url',
    ])
        ->and($data)->toHaveKeys([
            'eyebrow',
            'heading',
            'heading_accent',
            'body',
            'experience_value',
            'experience_label',
            'points',
            'button_label',
            'button_url',
            'founder_name',
            'founder_role',
        ])
        ->and($data['points'])->toHaveCount(4)
        ->and($data['experience_value'])->toBe('25+')
        ->and($data['eyebrow'])->toBe('Über uns');
});

test('about experience exposes the picker metadata shape', function (): void {
    $meta = AboutExperienceWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('about_experience')
        ->and($meta['label'])->toBe('About + Experience')
        ->and($meta['category'])->toBe('content');
});
