<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyFaq;
use App\Models\CompanyFaqTranslation;
use App\Models\District;
use App\Models\MediaFile;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Lives OUTSIDE the admin dashboard so it can later serve both admins
 * and self-service company owners.
 * Every field the CMS stores appears as a row: icon + label + short
 * preview of the current value. Each row's edit pencil currently
 * jumps to the full admin edit form; per-section modals are a
 * follow-up phase.
 */
final class CompanyProfileController extends Controller
{
    public function show(Company $company, ?string $slug = null): Response|RedirectResponse
    {
        $company->load([
            'primaryCity:id,state_id,name,permalink',
            'primaryCity.state:id,country_id,name,code',
            'primaryDistrict:id,city_id,name,permalink',
            'translations',
            'contacts',
            'services.translations',
            'serviceAreas.city:id,name',
            'media',
            'faqs.translations',
        ]);

        $locale = App::getLocale();
        $canonicalSlug = $this->canonicalSlug($company);
        if ($canonicalSlug !== null && $slug !== $canonicalSlug) {
            return redirect()->route('portal.company.profile', [
                'company' => $company->id,
                'slug' => $canonicalSlug,
            ], 301);
        }

        return Inertia::render('portal/CompanyProfile', [
            'locale' => $locale,
            'company' => $this->presentCompany($company, $locale),
            'rows' => $this->buildRows($company, $locale),
            'editUrl' => route('admin.companies.edit', $company->id),
            'availableServices' => Inertia::optional(fn (): array => $this->availableServices($locale)),
            'districtsByCity' => Inertia::optional(fn (): array => $this->availableDistrictsByCity()),
            'faqs' => Inertia::optional(fn (): array => $this->presentFaqs($company)),
            'gallery' => Inertia::optional(fn (): array => $this->presentGallery($company)),
            'selectedServiceIds' => Inertia::optional(fn (): array => $company->services()
                ->pluck('service_categories.id')->all()),
            'selectedDistrictIds' => Inertia::optional(fn (): array => $company->serviceAreas()
                ->pluck('districts.id')->all()),
        ]);
    }

    public function updateName(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.lang' => ['required', 'string', 'max:10'],
            'translations.*.name' => ['nullable', 'string', 'max:255'],
            'translations.*.permalink' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]*$/'],
        ]);

        foreach ($data['translations'] as $t) {
            $name = mb_trim((string) ($t['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $permalink = mb_trim((string) ($t['permalink'] ?? ''));
            if ($permalink === '') {
                $permalink = Str::slug($name);
            }

            $company->translations()->updateOrCreate(
                ['lang' => (string) $t['lang']],
                [
                    'name' => $name,
                    'permalink' => $permalink,
                ],
            );
        }

        return back(303);
    }

    /**
     * PUBLIC update for the Founded Year row.
     */
    public function updateFounded(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'founded_year' => ['nullable', 'integer', 'between:1800,'.date('Y')],
        ]);

        $company->update(['founded_year' => $data['founded_year'] ?? null]);

        return back(303);
    }

    /**
     * PUBLIC update for the Employees row.
     */
    public function updateEmployees(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'employee_count' => ['nullable', 'integer', 'between:0,100000'],
        ]);

        $company->update(['employee_count' => $data['employee_count'] ?? null]);

        return back(303);
    }

    /**
     * PUBLIC update for the Business Website row.
     */
    public function updateWebsite(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'website' => ['nullable', 'string', 'max:255', 'url'],
        ]);

        $url = mb_trim((string) ($data['website'] ?? ''));

        if ($url === '') {
            $company->contacts()->where('type', 'website')->delete();

            return back(303);
        }

        $company->contacts()->updateOrCreate(
            ['type' => 'website'],
            ['value' => $url, 'is_primary' => true],
        );

        return back(303);
    }

    /**
     * PUBLIC update for the Trust Badges row (verified + top-rated).
     */
    public function updateTrust(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'verified' => ['required', 'boolean'],
            'is_top_rated' => ['required', 'boolean'],
        ]);

        $company->update([
            'verified' => (bool) $data['verified'],
            'is_top_rated' => (bool) $data['is_top_rated'],
        ]);

        return back(303);
    }

    /**
     * PUBLIC update for the About / Description section.
     */
    public function updateAbout(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.lang' => ['required', 'string', 'max:10'],
            'translations.*.about' => ['nullable', 'string', 'max:10000'],
        ]);

        foreach ($data['translations'] as $t) {
            $lang = (string) $t['lang'];
            $about = (string) ($t['about'] ?? '');

            $existing = $company->translations()->where('lang', $lang)->first();
            if ($existing === null && $about === '') {
                continue;
            }

            $company->translations()->updateOrCreate(
                ['lang' => $lang],
                ['about' => $about !== '' ? $about : null],
            );
        }

        return back(303);
    }

    /**
     * PUBLIC update for the Business Address section.
     * PUBLIC update for the Short Description section — per-language
     * short pitch line shown on cards + search results.
     */
    public function updateShortDescription(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.lang' => ['required', 'string', 'max:10'],
            'translations.*.short_description' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($data['translations'] as $t) {
            $lang = (string) $t['lang'];
            $sd = mb_trim((string) ($t['short_description'] ?? ''));

            $existing = $company->translations()->where('lang', $lang)->first();
            if ($existing === null && $sd === '') {
                continue;
            }

            $company->translations()->updateOrCreate(
                ['lang' => $lang],
                ['short_description' => $sd !== '' ? $sd : null],
            );
        }

        return back(303);
    }

    /**
     * PUBLIC update for the Google Ratings row (rating 0–5 + count).
     * Both fields together — they only make sense as a pair.
     */
    public function updateGoogle(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'google_rating' => ['nullable', 'numeric', 'between:0,5'],
            'google_review_count' => ['nullable', 'integer', 'between:0,1000000'],
        ]);

        $company->update([
            'google_rating' => $data['google_rating'] ?? null,
            'google_review_count' => (int) ($data['google_review_count'] ?? 0),
        ]);

        return back(303);
    }

    public function updateAddress(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'street' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
        ]);

        $company->update([
            'street' => $data['street'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
        ]);

        return back(303);
    }

    /**
     * PUBLIC — sync the Business Categories (services) picker.
     */
    public function updateServices(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:service_categories,id'],
        ]);

        $company->services()->sync($data['service_ids'] ?? []);

        return back(303);
    }

    /**
     * PUBLIC — sync the Service Areas (districts) picker.
     */
    public function updateAreas(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'district_ids' => ['nullable', 'array'],
            'district_ids.*' => ['integer', 'exists:districts,id'],
        ]);

        $company->serviceAreas()->sync($data['district_ids'] ?? []);

        return back(303);
    }

    /**
     * PUBLIC — rewrite the FAQ list.
     */
    public function updateFaqs(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'faqs' => ['nullable', 'array'],
            'faqs.*.translations' => ['required', 'array', 'min:1'],
            'faqs.*.translations.*.lang' => ['required', 'string', 'max:10'],
            'faqs.*.translations.*.question' => ['required', 'string', 'max:500'],
            'faqs.*.translations.*.answer' => ['required', 'string', 'max:10000'],
        ]);

        $company->faqs()->each(fn (CompanyFaq $f) => $f->delete());

        foreach ($data['faqs'] ?? [] as $index => $f) {
            $faq = $company->faqs()->create([
                'sort_order' => $index + 1,
                'status' => 'published',
            ]);
            foreach ($f['translations'] as $t) {
                CompanyFaqTranslation::query()->create([
                    'faq_id' => $faq->id,
                    'lang' => (string) $t['lang'],
                    'question' => (string) $t['question'],
                    'answer' => (string) $t['answer'],
                ]);
            }
        }

        return back(303);
    }

    /**
     * PUBLIC — Logo + Cover uploads.
     */
    public function updateBranding(Request $request, Company $company): RedirectResponse
    {
        $request->validate([
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'cover' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_cover' => ['nullable', 'boolean'],
        ]);

        $updates = [];

        if ($request->boolean('remove_logo')) {
            $this->deleteBrandingFile((string) $company->logo);
            $updates['logo'] = null;
        }
        if ($request->hasFile('logo')) {
            $this->deleteBrandingFile((string) $company->logo);
            $updates['logo'] = $request->file('logo')->store('company-portal/branding', 'public');
        }

        if ($request->boolean('remove_cover')) {
            $this->deleteBrandingFile((string) $company->cover);
            $updates['cover'] = null;
        }
        if ($request->hasFile('cover')) {
            $this->deleteBrandingFile((string) $company->cover);
            $updates['cover'] = $request->file('cover')->store('company-portal/branding', 'public');
        }

        if ($updates !== []) {
            $company->update($updates);
        }

        return back(303);
    }

    /**
     * PUBLIC — upload a single Gallery image.
     */
    public function addGalleryImage(Request $request, Company $company): RedirectResponse
    {
        $request->validate([
            'image' => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['sometimes', 'array', 'max:20'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $files = $request->file('images', []);
        if (! is_array($files)) {
            $files = [];
        }
        if ($request->file('image') !== null) {
            $files[] = $request->file('image');
        }

        if ($files === []) {
            return back(303)->withErrors(['images' => __('validation.required', ['attribute' => 'images'])]);
        }

        DB::transaction(function () use ($files, $company): void {
            $nextOrder = ((int) $company->media()->where('kind', 'gallery')->max('sort_order')) + 1;

            foreach ($files as $file) {
                $path = $file->store('company-portal/gallery', 'public');

                $mediaFile = MediaFile::query()->create([
                    'folder_id' => null,
                    'user_id' => null,
                    'name' => $file->getClientOriginalName(),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => (string) $file->getMimeType(),
                    'extension' => (string) ($file->getClientOriginalExtension() ?: 'bin'),
                    'size' => (int) $file->getSize(),
                    'disk' => 'public',
                    'path' => $path,
                ]);

                $company->media()->create([
                    'media_file_id' => $mediaFile->id,
                    'kind' => 'gallery',
                    'sort_order' => $nextOrder++,
                ]);
            }
        });

        return back(303);
    }

    /**
     * PUBLIC — remove a gallery image (pivot row + backing file).
     */
    public function deleteGalleryImage(Company $company, int $media): RedirectResponse
    {
        $row = $company->media()->with('mediaFile')->where('id', $media)->first();
        if ($row === null) {
            return back(303);
        }

        $file = $row->mediaFile;
        $row->delete();
        if ($file !== null) {
            if ($file->disk === 'public' && $file->path !== null) {
                Storage::disk('public')->delete($file->path);
            }
            $file->delete();
        }

        return back(303);
    }

    /**
     * PUBLIC update for the Contact Details section.
     */
    public function updateContacts(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'contacts' => ['nullable', 'array'],
            'contacts.*.type' => ['required', 'string', 'in:phone,email,whatsapp'],
            'contacts.*.value' => ['required', 'string', 'max:255'],
            'contacts.*.label' => ['nullable', 'string', 'max:100'],
        ]);

        $company->contacts()->whereIn('type', ['phone', 'email', 'whatsapp'])->delete();

        foreach ($data['contacts'] ?? [] as $i => $c) {
            $company->contacts()->create([
                'type' => (string) $c['type'],
                'value' => mb_trim((string) $c['value']),
                'label' => isset($c['label']) ? mb_trim((string) $c['label']) : null,
                'is_primary' => $i === 0,
            ]);
        }

        return back(303);
    }

    /* ---------------------------------------------- new presenters */

    /**
     * Flat list of service categories with parent labels for the
     * services picker checkbox list. Kept minimal (id + name + parent
     * name) so the payload is small.
     *
     * @return array<int, array<string, mixed>>
     */
    private function availableServices(string $locale): array
    {
        return ServiceCategory::query()
            ->with(['translations', 'parentCategory'])
            ->orderBy('sort_order')
            ->get()
            ->map(function (ServiceCategory $s) use ($locale): array {
                $tr = $s->translations->firstWhere('lang', $locale) ?? $s->translations->first();

                return [
                    'id' => (int) $s->id,
                    'name' => (string) ($tr?->name ?? '—'),
                    'parent_name' => $s->parentCategory?->name,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Districts grouped by city — the shape the areas picker needs
     * to render collapsed sections per city.
     *
     * @return array<int, array<string, mixed>>
     */
    private function availableDistrictsByCity(): array
    {
        $cities = City::query()
            ->where('status', 'published')
            ->with(['districts' => fn ($q) => $q->where('status', 'published')->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return $cities
            ->filter(fn (City $c) => $c->districts->isNotEmpty())
            ->map(fn (City $c): array => [
                'city_id' => (int) $c->id,
                'city_name' => (string) $c->name,
                'districts' => $c->districts
                    ->map(fn (District $d) => [
                        'id' => (int) $d->id,
                        'name' => (string) $d->name,
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * FAQ payload for the modal — one row per FAQ, each row has
     * per-language question/answer.
     *
     * @return array<int, array<string, mixed>>
     */
    private function presentFaqs(Company $company): array
    {
        return $company->faqs()
            ->with('translations')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (CompanyFaq $f): array => [
                'id' => (int) $f->id,
                'sort_order' => (int) $f->sort_order,
                'translations' => $f->translations
                    ->map(fn ($t) => [
                        'lang' => (string) $t->lang,
                        'question' => (string) ($t->question ?? ''),
                        'answer' => (string) ($t->answer ?? ''),
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * Gallery image list for the modal — pivot id + public URL.
     *
     * @return array<int, array<string, mixed>>
     */
    private function presentGallery(Company $company): array
    {
        return $company->media()
            ->where('kind', 'gallery')
            ->with('mediaFile')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($m): array => [
                'id' => (int) $m->id,
                'name' => (string) ($m->mediaFile?->name ?? ''),
                'url' => $m->mediaFile !== null
                    ? '/storage/'.$m->mediaFile->path
                    : null,
            ])
            ->filter(fn (array $row) => $row['url'] !== null)
            ->values()
            ->all();
    }

    /**
     * Delete a branding file from disk if it's a portal upload.
     */
    private function deleteBrandingFile(string $path): void
    {
        if ($path === '') {
            return;
        }
        if (! Str::startsWith($path, 'company-portal/branding/')) {
            return;
        }
        Storage::disk('public')->delete($path);
    }

    /**
     * Pick a stable, readable URL slug for this company.
     */
    private function canonicalSlug(Company $company): ?string
    {
        $default = $company->translations
            ->firstWhere(fn ($t) => $t->lang === 'de' && ! empty($t->permalink));
        if ($default !== null) {
            return $default->permalink;
        }

        $any = $company->translations->first(fn ($t) => ! empty($t->permalink));

        return $any?->permalink;
    }

    /**
     * Slim payload for the page header (name, logo, cover, city).
     *
     * @return array<string, mixed>
     */
    private function presentCompany(Company $company, string $locale): array
    {
        $t = $company->translations->firstWhere('lang', $locale) ?? $company->translations->first();
        $websiteContact = $company->contacts->firstWhere('type', 'website');

        $publicUrl = null;
        if ($t?->permalink !== null && $t->permalink !== '') {
            $publicUrl = $locale === 'de'
                ? "/company/{$t->permalink}"
                : "/{$locale}/company/{$t->permalink}";
        }

        return [
            'id' => (int) $company->id,
            'name' => $t?->name ?? '—',
            'permalink' => $t?->permalink,
            'public_url' => $publicUrl,
            'logo' => $company->logo,
            'cover' => $company->cover,
            'city_name' => $company->primaryCity?->name,
            'verified' => (bool) $company->verified,
            'is_top_rated' => (bool) $company->is_top_rated,
            'translations' => $company->translations
                ->map(fn ($t) => [
                    'lang' => (string) $t->lang,
                    'name' => (string) ($t->name ?? ''),
                    'permalink' => (string) ($t->permalink ?? ''),
                    'about' => (string) ($t->about ?? ''),
                    'short_description' => (string) ($t->short_description ?? ''),
                ])
                ->values()
                ->all(),
            'founded_year' => $company->founded_year,
            'employee_count' => $company->employee_count,
            'website' => (string) ($websiteContact?->value ?? ''),
            'google_rating' => $company->google_rating,
            'google_review_count' => (int) $company->google_review_count,
            'street' => (string) ($company->street ?? ''),
            'postal_code' => (string) ($company->postal_code ?? ''),
            'contacts' => $company->contacts
                ->filter(fn ($c) => $c->type !== 'website')
                ->map(fn ($c) => [
                    'type' => (string) $c->type,
                    'value' => (string) ($c->value ?? ''),
                    'label' => (string) ($c->label ?? ''),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * Build one row per section. Kept as a simple array so the Vue side
     * stays purely presentational.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildRows(Company $company, string $locale): array
    {
        $t = $company->translations->firstWhere('lang', $locale) ?? $company->translations->first();

        $contactPreview = $company->contacts
            ->map(fn ($c) => (string) $c->value)
            ->filter(fn (string $v) => $v !== '')
            ->take(3)
            ->implode(' · ');

        $servicesList = $company->services
            ->map(function ($s) use ($locale) {
                $st = $s->translations->firstWhere('lang', $locale) ?? $s->translations->first();

                return $st?->name;
            })
            ->filter()
            ->values();
        $servicesPreview = $servicesList->count() > 0
            ? $servicesList->take(3)->implode(', ').($servicesList->count() > 3
                ? ' +'.($servicesList->count() - 3).' '.__('admin.profile.more')
                : '')
            : null;

        $areasCount = $company->serviceAreas->count();
        $areasPreview = $areasCount > 0
            ? trans_choice('admin.profile.areas_preview', $areasCount, ['n' => $areasCount])
            : null;

        $addressParts = array_filter([
            $company->street,
            mb_trim((string) $company->postal_code.' '.(string) ($company->primaryCity?->name ?? '')),
        ]);
        $addressPreview = $addressParts === []
            ? null
            : implode(', ', $addressParts);

        $about = $t?->about ?? '';
        $aboutPreview = $about !== ''
            ? mb_substr(strip_tags($about), 0, 120).(mb_strlen(strip_tags($about)) > 120 ? '…' : '')
            : null;

        $shortDescription = (string) ($t?->short_description ?? '');
        $shortDescriptionPreview = $shortDescription !== ''
            ? mb_substr($shortDescription, 0, 120).(mb_strlen($shortDescription) > 120 ? '…' : '')
            : null;

        $googlePreview = null;
        if ($company->google_rating !== null || $company->google_review_count > 0) {
            $parts = [];
            if ($company->google_rating !== null) {
                $parts[] = number_format((float) $company->google_rating, 1);
            }
            if ($company->google_review_count > 0) {
                $parts[] = trans_choice(
                    'admin.profile.google_reviews_preview',
                    (int) $company->google_review_count,
                    ['n' => (int) $company->google_review_count]
                );
            }
            $googlePreview = implode(' · ', $parts);
        }

        $faqCount = $company->faqs->count();

        $galleryCount = $company->media->where('collection', 'gallery')->count();
        $galleryPreview = $galleryCount > 0
            ? trans_choice('admin.profile.gallery_preview', $galleryCount, ['n' => $galleryCount])
            : null;

        $trustParts = [];
        if ($company->verified) {
            $trustParts[] = __('admin.profile.trust_verified');
        }
        if ($company->is_top_rated) {
            $trustParts[] = __('admin.profile.trust_top_rated');
        }
        $trustPreview = $trustParts === [] ? null : implode(' · ', $trustParts);

        return [
            [
                'id' => 'contacts',
                'label' => __('admin.profile.rows.contacts'),
                'icon' => 'Phone',
                'preview' => $contactPreview !== '' ? $contactPreview : null,
                'is_missing' => $company->contacts->isEmpty(),
                'edit_anchor' => 'contacts',
            ],
            [
                'id' => 'address',
                'label' => __('admin.profile.rows.address'),
                'icon' => 'MapPin',
                'preview' => $addressPreview,
                'is_missing' => $addressPreview === null,
                'edit_anchor' => 'address',
            ],
            [
                'id' => 'services',
                'label' => __('admin.profile.rows.services'),
                'icon' => 'Layers',
                'preview' => $servicesPreview,
                'is_missing' => $servicesList->isEmpty(),
                'edit_anchor' => 'services',
            ],
            [
                'id' => 'areas',
                'label' => __('admin.profile.rows.areas'),
                'icon' => 'Map',
                'preview' => $areasPreview,
                'is_missing' => $areasCount === 0,
                'edit_anchor' => 'service-areas',
            ],
            [
                'id' => 'founded',
                'label' => __('admin.profile.rows.founded'),
                'icon' => 'Calendar',
                'preview' => $company->founded_year !== null ? (string) $company->founded_year : null,
                'is_missing' => $company->founded_year === null,
                'edit_anchor' => 'basic',
            ],
            [
                'id' => 'employees',
                'label' => __('admin.profile.rows.employees'),
                'icon' => 'Users',
                'preview' => $company->employee_count !== null ? (string) $company->employee_count : null,
                'is_missing' => $company->employee_count === null,
                'edit_anchor' => 'basic',
            ],
            [
                'id' => 'short_description',
                'label' => __('admin.profile.rows.short_description'),
                'icon' => 'AlignLeft',
                'preview' => $shortDescriptionPreview,
                'is_missing' => $shortDescriptionPreview === null,
                'edit_anchor' => 'translations',
            ],
            [
                'id' => 'about',
                'label' => __('admin.profile.rows.about'),
                'icon' => 'FileText',
                'preview' => $aboutPreview,
                'is_missing' => $aboutPreview === null,
                'edit_anchor' => 'translations',
            ],
            [
                'id' => 'faqs',
                'label' => __('admin.profile.rows.faqs'),
                'icon' => 'HelpCircle',
                'preview' => $faqCount > 0
                    ? trans_choice('admin.profile.faqs_preview', $faqCount, ['n' => $faqCount])
                    : null,
                'is_missing' => false,
                'edit_anchor' => 'faqs',
            ],
            [
                'id' => 'gallery',
                'label' => __('admin.profile.rows.gallery'),
                'icon' => 'Image',
                'preview' => $galleryPreview,
                'is_missing' => $galleryCount === 0,
                'edit_anchor' => 'media',
            ],
            [
                'id' => 'branding',
                'label' => __('admin.profile.rows.branding'),
                'icon' => 'ImagePlus',
                'preview' => match (true) {
                    $company->logo !== null && $company->cover !== null => __('admin.profile.branding_both'),
                    $company->logo !== null => __('admin.profile.branding_logo_only'),
                    $company->cover !== null => __('admin.profile.branding_cover_only'),
                    default => null,
                },
                'is_missing' => $company->logo === null && $company->cover === null,
                'edit_anchor' => 'branding',
            ],
            [
                'id' => 'trust',
                'label' => __('admin.profile.rows.trust'),
                'icon' => 'BadgeCheck',
                'preview' => $trustPreview,
                'is_missing' => false,
                'edit_anchor' => 'basic',
            ],
            [
                'id' => 'google',
                'label' => __('admin.profile.rows.google'),
                'icon' => 'Star',
                'preview' => $googlePreview,
                'is_missing' => $googlePreview === null,
                'edit_anchor' => 'basic',
            ],
        ];
    }
}
