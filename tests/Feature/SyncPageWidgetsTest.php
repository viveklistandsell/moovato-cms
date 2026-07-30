<?php

declare(strict_types=1);

use App\Actions\Admin\Page\Widget\SyncPageWidgets;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageWidget;
use App\Models\PageWidgetTranslation;
use App\Widgets\Registry\WidgetRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Language::query()->create([
        'code' => 'de',
        'name' => 'German',
        'native_name' => 'Deutsch',
        'lang_is_default' => true,
        'status' => true,
        'sort_order' => 1,
    ]);
    Language::query()->create([
        'code' => 'en',
        'name' => 'English',
        'native_name' => 'English',
        'lang_is_default' => false,
        'status' => true,
        'sort_order' => 2,
    ]);

    $this->page = Page::query()->create([
        'title' => 'Home',
        'permalink' => 'home',
        'content' => null,
        'template' => 'default',
        'is_home' => true,
        'status' => 'published',
    ]);
    $this->action = app(SyncPageWidgets::class);
});

test('it creates widgets with per-language translations and assigns positions', function (): void {
    $this->action->handle($this->page, [
        [
            'type' => 'hero',
            'settings' => ['alignment' => 'center', 'overlay' => true, 'height' => 'lg', 'image_path' => null, 'image_url' => null],
            'translations' => [
                'de' => ['title' => 'Willkommen'],
                'en' => ['title' => 'Welcome'],
            ],
        ],
        [
            'type' => 'cta_banner',
            'settings' => ['image_path' => null, 'image_url' => null],
            'translations' => [
                'de' => ['heading' => 'Jetzt starten'],
                'en' => ['heading' => 'Get started'],
            ],
        ],
    ]);

    $widgets = $this->page->widgets()->orderBy('position')->get();

    expect($widgets)->toHaveCount(2)
        ->and($widgets[0]->type)->toBe('hero')
        ->and($widgets[0]->position)->toBe(0)
        ->and($widgets[1]->type)->toBe('cta_banner')
        ->and($widgets[1]->position)->toBe(1);

    expect(PageWidgetTranslation::query()->where('page_widget_id', $widgets[0]->id)->count())->toBe(2);

    $de = PageWidgetTranslation::query()
        ->where('page_widget_id', $widgets[0]->id)
        ->where('lang', 'de')
        ->first();
    expect($de->data['title'])->toBe('Willkommen');
});

test('it updates an existing widget by id and rewrites its translations', function (): void {
    $this->action->handle($this->page, [
        [
            'type' => 'text_block',
            'settings' => ['width' => 'narrow', 'alignment' => 'left', 'background' => 'none'],
            'translations' => [
                'de' => ['heading' => 'Original', 'body' => '<p>old</p>'],
                'en' => ['heading' => 'Original EN'],
            ],
        ],
    ]);

    $existing = PageWidget::query()->where('page_id', $this->page->id)->first();

    $this->action->handle($this->page, [
        [
            'id' => $existing->id,
            'type' => 'text_block',
            'settings' => ['width' => 'wide', 'alignment' => 'center', 'background' => 'muted'],
            'translations' => [
                'de' => ['heading' => 'Updated', 'body' => '<p>new</p>'],
                // EN deliberately omitted — should be deleted.
            ],
        ],
    ]);

    $existing->refresh();
    expect($existing->settings['width'])->toBe('wide');

    $de = PageWidgetTranslation::query()
        ->where('page_widget_id', $existing->id)->where('lang', 'de')->first();
    $en = PageWidgetTranslation::query()
        ->where('page_widget_id', $existing->id)->where('lang', 'en')->first();

    expect($de->data['heading'])->toBe('Updated')
        ->and($en)->toBeNull();
});

test('it deletes widgets that are missing from the incoming stack', function (): void {
    $this->action->handle($this->page, [
        ['type' => 'hero', 'settings' => ['alignment' => 'center', 'overlay' => true, 'height' => 'lg'], 'translations' => ['de' => ['title' => 'A']]],
        ['type' => 'text_block', 'settings' => ['width' => 'narrow', 'alignment' => 'left', 'background' => 'none'], 'translations' => ['de' => ['heading' => 'B']]],
    ]);

    expect($this->page->widgets()->count())->toBe(2);

    // Now resync with only the first widget — the text block should be removed.
    $hero = $this->page->widgets()->where('type', 'hero')->first();
    $this->action->handle($this->page, [
        ['id' => $hero->id, 'type' => 'hero', 'settings' => $hero->settings, 'translations' => ['de' => ['title' => 'A']]],
    ]);

    expect($this->page->widgets()->count())->toBe(1)
        ->and(PageWidget::query()->where('page_id', $this->page->id)->where('type', 'text_block')->count())->toBe(0);
});

test('it ignores unknown widget types instead of failing', function (): void {
    $this->action->handle($this->page, [
        ['type' => 'nonexistent', 'settings' => [], 'translations' => []],
        ['type' => 'hero', 'settings' => [], 'translations' => ['de' => ['title' => 'Real']]],
    ]);

    $only = $this->page->widgets()->get();

    expect($only)->toHaveCount(1)
        ->and($only->first()->type)->toBe('hero');
});

test('every registered widget exposes a valid metadata shape', function (): void {
    $registry = app(WidgetRegistry::class);

    foreach ($registry->all() as $meta) {
        expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data']);
        expect($meta['type'])->toBeString()->not->toBe('');
        expect($meta['default_settings'])->toBeArray();
        expect($meta['default_data'])->toBeArray();
    }
});
