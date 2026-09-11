<?php

declare(strict_types=1);

use App\Widgets\DarkFeature\DarkFeatureWidget;
use App\Widgets\MediaChecklist\MediaChecklistWidget;
use App\Widgets\Registry\WidgetRegistry;
use App\Widgets\StatFeatures\StatFeaturesWidget;
use App\Widgets\TeamCta\TeamCtaWidget;
use App\Widgets\TextColumns\TextColumnsWidget;

test('the new content section widgets are registered under their slugs', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->resolve('media_checklist'))->toBe(MediaChecklistWidget::class)
        ->and($registry->resolve('text_columns'))->toBe(TextColumnsWidget::class)
        ->and($registry->resolve('dark_feature'))->toBe(DarkFeatureWidget::class)
        ->and($registry->resolve('team_cta'))->toBe(TeamCtaWidget::class)
        ->and($registry->resolve('stat_features'))->toBe(StatFeaturesWidget::class);
});

test('media checklist exposes image-side, card style and a checklist body', function (): void {
    $settings = MediaChecklistWidget::defaultSettings();
    $data = MediaChecklistWidget::defaultData();

    expect($settings)->toHaveKeys(['image_path', 'image_url', 'image_side', 'card'])
        ->and($settings['image_side'])->toBe('left')
        ->and($data)->toHaveKeys(['eyebrow', 'heading', 'body', 'button_label', 'button_url'])
        ->and($data['body'])->toContain('<ul>');
});

test('text columns exposes a two column body and checklist', function (): void {
    $data = TextColumnsWidget::defaultData();

    expect($data)->toHaveKeys(['heading', 'subheading', 'body', 'intro', 'outro'])
        ->and($data['intro'])->toContain('<ul>');
});

test('dark feature exposes an image side, two column list and button', function (): void {
    $settings = DarkFeatureWidget::defaultSettings();
    $data = DarkFeatureWidget::defaultData();

    expect($settings)->toHaveKeys(['image_path', 'image_url', 'image_side'])
        ->and($data)->toHaveKeys(['heading', 'body', 'list_title', 'points', 'button_label', 'button_url'])
        ->and($data['points'])->not->toBeEmpty();
});

test('team cta exposes a checklist and an embedded hiring card', function (): void {
    $data = TeamCtaWidget::defaultData();

    expect($data)->toHaveKeys([
        'heading',
        'body',
        'points',
        'cta_title',
        'cta_body',
        'cta_button_label',
        'cta_button_url',
    ])
        ->and($data['points'])->not->toBeEmpty();
});

test('stat features exposes a numbered checklist body and a stat badge', function (): void {
    $data = StatFeaturesWidget::defaultData();

    expect($data)->toHaveKeys(['heading', 'body', 'stat_value', 'stat_label'])
        ->and($data['body'])->toContain('<ol>')
        ->and($data['stat_value'])->toBe('1.5k+');
});

test('the new content section widgets expose the picker metadata shape', function (): void {
    $widgets = [
        MediaChecklistWidget::class => 'media_checklist',
        TextColumnsWidget::class => 'text_columns',
        DarkFeatureWidget::class => 'dark_feature',
        TeamCtaWidget::class => 'team_cta',
        StatFeaturesWidget::class => 'stat_features',
    ];

    foreach ($widgets as $class => $type) {
        $meta = $class::toArray();

        expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
            ->and($meta['type'])->toBe($type)
            ->and($meta['category'])->toBe('content');
    }
});
