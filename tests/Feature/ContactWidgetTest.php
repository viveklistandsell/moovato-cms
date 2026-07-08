<?php

declare(strict_types=1);

use App\Widgets\Contact\ContactWidget;
use App\Widgets\Registry\WidgetRegistry;

test('contact widget is registered under the contact slug', function (): void {
    expect(app(WidgetRegistry::class)->resolve('contact'))->toBe(ContactWidget::class);
});

test('contact widget exposes editable copy, contact details and a service list', function (): void {
    $data = ContactWidget::defaultData();

    expect($data)->toHaveKeys([
        'eyebrow',
        'heading_lead',
        'heading_highlight',
        'description',
        'email',
        'phone',
        'address',
        'phone_title',
        'email_title',
        'location_title',
        'services',
        'service_placeholder',
        'submit_label',
        'success_title',
        'success_text',
        'watermark',
    ])
        ->and($data['email'])->toBe('hallo@moovato.de')
        ->and($data['services'])->toBe(['Privat', 'Gewerbe', 'Fernumzug', 'Spezialtransport'])
        ->and(ContactWidget::defaultSettings())->toHaveKey('map_embed_url');
});

test('contact widget exposes the picker metadata shape', function (): void {
    $meta = ContactWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('contact')
        ->and($meta['label'])->toBe('Contact')
        ->and($meta['category'])->toBe('content');
});
