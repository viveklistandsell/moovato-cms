<?php

declare(strict_types=1);

namespace App\Actions\Admin\SiteSetting;

use App\Models\SiteSetting;
use App\Models\SiteSettingTranslation;
use Illuminate\Support\Facades\DB;

final readonly class UpdateSiteSetting
{
    /**
     * Per-locale translation columns mirrored by the form. Adding a new
     * translatable field? Drop its key here and the upsert below picks it
     * up — no further wiring needed.
     */
    private const TRANSLATABLE_FIELDS = [
        'about_text',
        'site_tagline',
        'default_meta_description',
        'maintenance_heading',
        'maintenance_message',
    ];

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): SiteSetting
    {
        return DB::transaction(function () use ($data): SiteSetting {
            if (isset($data['whatsapp']) && is_string($data['whatsapp'])) {
                $digits = preg_replace('/\D+/', '', $data['whatsapp']) ?? '';
                $data['whatsapp'] = $digits === '' ? null : $digits;
            }

            /** @var array<int, array<string, mixed>> $translations */
            $translations = $data['translations'] ?? [];
            unset($data['translations']);

            foreach (['privacy_page_id', 'terms_page_id', 'imprint_page_id'] as $fk) {
                if (array_key_exists($fk, $data) && ($data[$fk] === '' || $data[$fk] === 0)) {
                    $data[$fk] = null;
                }
            }

            $settings = SiteSetting::query()->firstOrNew(['id' => 1]);
            $settings->fill($data);
            $settings->save();

            foreach ($translations as $row) {
                $attrs = [];
                foreach (self::TRANSLATABLE_FIELDS as $field) {
                    if (array_key_exists($field, $row)) {
                        $value = $row[$field];
                        $attrs[$field] = $value === '' ? null : $value;
                    }
                }

                SiteSettingTranslation::query()->updateOrCreate(
                    ['site_setting_id' => $settings->id, 'lang' => $row['lang']],
                    $attrs,
                );
            }

            SiteSetting::flush();

            return $settings;
        });
    }
}
