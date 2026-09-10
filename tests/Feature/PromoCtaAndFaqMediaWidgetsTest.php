<?php

declare(strict_types=1);

use App\Widgets\FaqMedia\FaqMediaWidget;
use App\Widgets\PromoCta\PromoCtaWidget;
use App\Widgets\Registry\WidgetRegistry;

test('both new widgets are registered with their type slugs', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('promo_cta'))->toBeTrue()
        ->and($registry->has('faq_media'))->toBeTrue()
        ->and($registry->resolve('promo_cta'))->toBe(PromoCtaWidget::class)
        ->and($registry->resolve('faq_media'))->toBe(FaqMediaWidget::class);
});

test('promo cta ships a checklist body, call block and is a marketing widget', function (): void {
    $data = PromoCtaWidget::defaultData();

    expect($data['description'])->toContain('<ul>')
        ->and($data)->toHaveKeys(['heading', 'button_label', 'badge_number', 'call_label', 'phone'])
        ->and($data['phone'])->toBe('030 1234 5678')
        ->and(PromoCtaWidget::category())->toBe('marketing');
});

test('faq media ships an image slot, highlighted heading and questions', function (): void {
    $settings = FaqMediaWidget::defaultSettings();
    $data = FaqMediaWidget::defaultData();

    expect($settings)->toHaveKeys(['image_path', 'image_url', 'first_open'])
        ->and($data)->toHaveKeys(['vertical_label', 'eyebrow', 'heading_lead', 'heading_highlight', 'heading_tail', 'items'])
        ->and($data['heading_highlight'])->toBe('Fragen')
        ->and($data['items'])->toHaveCount(4)
        ->and($data['items'][0])->toHaveKeys(['question', 'answer']);
});

test('faq media keeps a distinct type from the original faq widget', function (): void {
    expect(FaqMediaWidget::type())->toBe('faq_media')
        ->and(FaqMediaWidget::type())->not->toBe('faq');
});

test('both widgets expose the picker metadata shape', function (): void {
    foreach ([PromoCtaWidget::class, FaqMediaWidget::class] as $widget) {
        $meta = $widget::toArray();
        expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
            ->and($meta['type'])->toBeString()->not->toBe('');
    }
});
