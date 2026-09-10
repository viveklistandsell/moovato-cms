<?php

declare(strict_types=1);

use App\Widgets\DarkIntro\DarkIntroWidget;
use App\Widgets\Registry\WidgetRegistry;
use App\Widgets\SupportingMedia\SupportingMediaWidget;
use App\Widgets\WhyChooseMedia\WhyChooseMediaWidget;

test('the dedicated screenshot widgets are registered under their slugs', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->resolve('why_choose_media'))->toBe(WhyChooseMediaWidget::class)
        ->and($registry->resolve('supporting_media'))->toBe(SupportingMediaWidget::class)
        ->and($registry->resolve('dark_intro'))->toBe(DarkIntroWidget::class);
});

test('why choose media defaults to an image on the right with no button', function (): void {
    $settings = WhyChooseMediaWidget::defaultSettings();
    $data = WhyChooseMediaWidget::defaultData();

    expect($settings['image_side'])->toBe('right')
        ->and($settings['card'])->toBeFalse()
        ->and($data['body'])->toContain('<ul>')
        ->and($data['button_label'])->toBe('');
});

test('supporting media defaults to an image on the left with paragraph body', function (): void {
    $settings = SupportingMediaWidget::defaultSettings();
    $data = SupportingMediaWidget::defaultData();

    expect($settings['image_side'])->toBe('left')
        ->and($data['body'])->toContain('<ul>');
});

test('dark intro defaults to no list and a button', function (): void {
    $data = DarkIntroWidget::defaultData();

    expect($data['points'])->toBe([])
        ->and($data['button_label'])->toBe('Mehr erfahren');
});

test('the dedicated screenshot widgets expose the picker metadata shape', function (): void {
    $widgets = [
        WhyChooseMediaWidget::class => 'why_choose_media',
        SupportingMediaWidget::class => 'supporting_media',
        DarkIntroWidget::class => 'dark_intro',
    ];

    foreach ($widgets as $class => $type) {
        $meta = $class::toArray();

        expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
            ->and($meta['type'])->toBe($type)
            ->and($meta['category'])->toBe('content');
    }
});
