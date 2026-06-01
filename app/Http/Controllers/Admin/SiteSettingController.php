<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\SiteSetting\UpdateSiteSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteSetting\UpdateSiteSettingRequest;
use App\Models\Language;
use App\Models\SiteSetting;
use App\Models\SiteSettingTranslation;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Single-form CRUD on the one site_settings row that drives the public
 * footer (About text per locale, contact info, WhatsApp, socials). No
 * create / delete endpoints — the row is owned by the seeder.
 */
final class SiteSettingController extends Controller
{
    public function edit(): Response
    {
        $settings = SiteSetting::query()
            ->with('translations')
            ->firstOrNew(['id' => 1]);

        return Inertia::render('admin/site-settings/Edit', [
            'settings' => [
                'address' => $settings->address,
                'phone' => $settings->phone,
                'email' => $settings->email,
                'whatsapp' => $settings->whatsapp,
                'facebook_url' => $settings->facebook_url,
                'twitter_url' => $settings->twitter_url,
                'linkedin_url' => $settings->linkedin_url,
                'instagram_url' => $settings->instagram_url,
                // Map of lang → about_text. The form serializes this back
                // into an array of { lang, about_text } rows on submit.
                'translations' => $settings->relationLoaded('translations')
                    ? $settings->translations
                        ->mapWithKeys(fn (SiteSettingTranslation $t): array => [
                            $t->lang => ['about_text' => $t->about_text],
                        ])
                        ->all()
                    : [],
            ],
            'languages' => $this->presentLanguages(),
        ]);
    }

    public function update(
        UpdateSiteSettingRequest $request,
        UpdateSiteSetting $action,
    ): RedirectResponse {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $action->handle($data);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Site settings updated.',
        ]);
    }

    /**
     * @return array<int, array{code: string, name: string, native_name: string, flag: ?string, is_default: bool}>
     */
    private function presentLanguages(): array
    {
        return Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Language $lang): array => [
                'code' => $lang->code,
                'name' => $lang->name,
                'native_name' => $lang->native_name,
                'flag' => $lang->flag,
                'is_default' => $lang->lang_is_default,
            ])
            ->values()
            ->all();
    }
}
