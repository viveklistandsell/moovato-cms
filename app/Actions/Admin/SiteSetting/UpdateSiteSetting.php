<?php

declare(strict_types=1);

namespace App\Actions\Admin\SiteSetting;

use App\Models\SiteSetting;
use App\Models\SiteSettingTranslation;
use Illuminate\Support\Facades\DB;

final readonly class UpdateSiteSetting
{
    /**
     * @param  array{
     *   address?: ?string,
     *   phone?: ?string,
     *   email?: ?string,
     *   whatsapp?: ?string,
     *   facebook_url?: ?string,
     *   twitter_url?: ?string,
     *   linkedin_url?: ?string,
     *   instagram_url?: ?string,
     *   translations: array<int, array{lang: string, about_text?: ?string}>,
     * }  $data
     */
    public function handle(array $data): SiteSetting
    {
        return DB::transaction(function () use ($data): SiteSetting {
            if (isset($data['whatsapp']) && is_string($data['whatsapp'])) {
                $digits = preg_replace('/\D+/', '', $data['whatsapp']) ?? '';
                $data['whatsapp'] = $digits === '' ? null : $digits;
            }

            $translations = $data['translations'];
            unset($data['translations']);

            $settings = SiteSetting::query()->firstOrNew(['id' => 1]);
            $settings->fill($data);
            $settings->save();

            foreach ($translations as $row) {
                SiteSettingTranslation::query()->updateOrCreate(
                    ['site_setting_id' => $settings->id, 'lang' => $row['lang']],
                    ['about_text' => $row['about_text'] ?? null],
                );
            }

            SiteSetting::flush();

            return $settings;
        });
    }
}
