<?php

declare(strict_types=1);

use App\Models\Language;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());

    Language::query()->create([
        'code' => 'de',
        'name' => 'German',
        'native_name' => 'Deutsch',
        'lang_is_default' => true,
        'status' => true,
        'sort_order' => 1,
    ]);
});

function makePage(): Page
{
    return Page::query()->create([
        'title' => 'Startseite',
        'permalink' => 'startseite',
        'content' => null,
        'template' => 'default',
        'is_home' => false,
        'status' => 'published',
    ]);
}

test('save and exit persists the widget stack through the page update endpoint', function (): void {
    $page = makePage();

    $this->put(route('admin.pages.update', $page), [
        'template' => 'default',
        'status' => 'published',
        'is_home' => false,
        'translations' => [
            'de' => ['title' => 'Startseite', 'permalink' => 'startseite'],
        ],
        'widgets' => [
            [
                'type' => 'cta_banner',
                'settings' => ['image_path' => null, 'image_url' => null],
                'translations' => ['de' => ['heading' => 'Jetzt buchen']],
            ],
        ],
    ])->assertRedirect(route('admin.pages.index'));

    $widgets = $page->fresh()->widgets()->get();

    expect($widgets)->toHaveCount(1)
        ->and($widgets[0]->type)->toBe('cta_banner')
        ->and($widgets[0]->position)->toBe(0);

    $translation = $widgets[0]->translations()->where('lang', 'de')->first();
    expect($translation->data['heading'])->toBe('Jetzt buchen');
});

test('an update that omits the widgets key leaves the existing stack untouched', function (): void {
    $page = makePage();

    $this->put(route('admin.pages.update', $page), [
        'template' => 'default',
        'status' => 'published',
        'is_home' => false,
        'translations' => ['de' => ['title' => 'Startseite', 'permalink' => 'startseite']],
        'widgets' => [
            ['type' => 'cta_banner', 'settings' => ['image_path' => null, 'image_url' => null]],
        ],
    ]);

    expect($page->fresh()->widgets()->count())->toBe(1);

    $this->put(route('admin.pages.update', $page), [
        'template' => 'default',
        'status' => 'draft',
        'is_home' => false,
        'translations' => ['de' => ['title' => 'Startseite', 'permalink' => 'startseite']],
    ]);

    expect($page->fresh()->widgets()->count())->toBe(1)
        ->and($page->fresh()->status)->toBe('draft');
});

test('sending an empty widgets array clears the stack', function (): void {
    $page = makePage();

    $this->put(route('admin.pages.update', $page), [
        'template' => 'default',
        'status' => 'published',
        'is_home' => false,
        'translations' => ['de' => ['title' => 'Startseite', 'permalink' => 'startseite']],
        'widgets' => [
            ['type' => 'cta_banner', 'settings' => ['image_path' => null, 'image_url' => null]],
        ],
    ]);

    expect($page->fresh()->widgets()->count())->toBe(1);

    $this->put(route('admin.pages.update', $page), [
        'template' => 'default',
        'status' => 'published',
        'is_home' => false,
        'translations' => ['de' => ['title' => 'Startseite', 'permalink' => 'startseite']],
        'widgets' => [],
    ]);

    expect($page->fresh()->widgets()->count())->toBe(0);
});
