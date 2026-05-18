<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

final class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            [
                'name' => 'German',
                'native_name' => 'Deutsch',
                'code' => 'de',
                'flag' => '🇩🇪',
                'status' => true,
                'lang_locale' => 'de_DE',
                'lang_is_default' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'English',
                'native_name' => 'English',
                'code' => 'en',
                'flag' => '🇬🇧',
                'status' => true,
                'lang_locale' => 'en_US',
                'lang_is_default' => false,
                'sort_order' => 2,
            ],
        ];

        foreach ($languages as $language) {
            Language::query()->updateOrCreate(
                ['code' => $language['code']],
                $language,
            );
        }
    }
}
