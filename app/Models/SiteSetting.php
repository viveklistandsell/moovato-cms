<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

#[Fillable([
    'about_text',
    'address',
    'phone',
    'email',
    'whatsapp',
    'facebook_url',
    'twitter_url',
    'linkedin_url',
    'instagram_url',
])]
final class SiteSetting extends Model
{
    private const CACHE_KEY = 'site_settings:current';
    public static function current(): self
    {
        /** @var array{attrs: array<string, mixed>, translations: array<int, array<string, mixed>>} $payload */
        $payload = Cache::remember(
            self::CACHE_KEY,
            3600,
            fn (): array => self::buildPayload(),
        );

        $model = (new self)->forceFill($payload['attrs']);

        $rel = $model->newCollection(
            array_map(
                fn (array $row): SiteSettingTranslation => (new SiteSettingTranslation)->forceFill($row),
                $payload['translations'],
            ),
        );
        $model->setRelation('translations', $rel);

        return $model;
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SiteSettingTranslation::class);
    }

    public function translation(?string $lang = null): ?SiteSettingTranslation
    {
        $lang ??= app()->getLocale();

        return $this->translations->firstWhere('lang', $lang)
            ?? $this->translations->first();
    }

    /**
     * @return array{attrs: array<string, mixed>, translations: array<int, array<string, mixed>>}
     */
    private static function buildPayload(): array
    {
        $row = self::query()->with('translations')->first();

        if ($row === null) {
            return ['attrs' => [], 'translations' => []];
        }

        return [
            'attrs' => $row->getAttributes(),
            'translations' => $row->translations
                ->map(fn (SiteSettingTranslation $t): array => $t->getAttributes())
                ->all(),
        ];
    }
}
