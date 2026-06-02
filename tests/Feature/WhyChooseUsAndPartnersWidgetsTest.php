<?php

declare(strict_types=1);

use App\Widgets\Partners\PartnersWidget;
use App\Widgets\Registry\WidgetRegistry;
use App\Widgets\WhyChooseUs\WhyChooseUsWidget;

test('both new widgets are registered with their type slugs', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('why_choose_us'))->toBeTrue()
        ->and($registry->has('partners'))->toBeTrue()
        ->and($registry->resolve('why_choose_us'))->toBe(WhyChooseUsWidget::class)
        ->and($registry->resolve('partners'))->toBe(PartnersWidget::class);
});

test('why_choose_us exposes a sticky image slot and five german cards', function (): void {
    $settings = WhyChooseUsWidget::defaultSettings();
    $data = WhyChooseUsWidget::defaultData();

    expect($settings)->toHaveKeys(['image_path', 'image_url'])
        ->and($data['cards'])->toHaveCount(5)
        ->and($data['cards'][0])->toHaveKeys(['icon', 'title', 'description'])
        ->and($data['cards'][0]['title'])->toBe('Festpreisgarantie')
        ->and($data['eyebrow'])->toBe('Warum Moovato');
});

test('why_choose_us validates image settings and card items', function (): void {
    expect(WhyChooseUsWidget::settingsRules())
        ->toHaveKeys(['image_path', 'image_url'])
        ->and(WhyChooseUsWidget::dataRules())
        ->toHaveKeys(['cards', 'cards.*.icon', 'cards.*.title', 'cards.*.description']);
});

test('partners ships logo entries and certificate badges as defaults', function (): void {
    $settings = PartnersWidget::defaultSettings();
    $data = PartnersWidget::defaultData();

    expect($settings['partners'])->not->toBeEmpty()
        ->and($settings['partners'][0])->toHaveKeys(['path', 'url', 'name', 'link'])
        ->and($settings['certificates'])->not->toBeEmpty()
        ->and($settings['certificates'][0])->toHaveKeys(['icon', 'title'])
        ->and($data)->toHaveKeys(['eyebrow', 'heading', 'subheading', 'partners_title', 'certificates_title']);
});

test('partners validates nested logo and certificate rules', function (): void {
    $rules = PartnersWidget::settingsRules();

    expect($rules)->toHaveKeys([
        'partners',
        'partners.*.path',
        'partners.*.url',
        'partners.*.name',
        'certificates',
        'certificates.*.icon',
        'certificates.*.title',
    ]);
});

test('both widgets expose the picker metadata shape', function (): void {
    foreach ([WhyChooseUsWidget::class, PartnersWidget::class] as $widget) {
        $meta = $widget::toArray();
        expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
            ->and($meta['type'])->toBeString()->not->toBe('')
            ->and($meta['category'])->toBe('content');
    }
});
