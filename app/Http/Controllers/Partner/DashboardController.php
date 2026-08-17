<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\CompanyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Post-login landing for a partner (company user).
 *
 * Shows a real overview of the partner's listing: profile
 * completeness, review pulse, gallery / FAQ counts, latest reviews
 * to reply to, and quick actions that jump into the portal editor.
 */
final class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var CompanyUser|null $user */
        $user = $request->user('company');

        if ($user === null) {
            return Inertia::render('partner/Dashboard', [
                'locale' => App::getLocale(),
                'user' => null,
                'company' => null,
                'completeness' => null,
                'stats' => null,
                'recentReviews' => [],
                'quickActions' => [],
            ]);
        }

        $company = $this->loadCompany($user);
        $locale = App::getLocale();

        return Inertia::render('partner/Dashboard', [
            'locale' => $locale,
            'user' => $this->presentUser($user),
            'company' => $company === null ? null : $this->presentCompany($company, $locale),
            'completeness' => $company === null ? null : $this->presentCompleteness($company, $locale),
            'stats' => $company === null ? null : $this->presentStats($company),
            'recentReviews' => $company === null ? [] : $this->presentRecentReviews($company),
            'quickActions' => $company === null ? [] : $this->presentQuickActions($company, $locale),
        ]);
    }

    /* ---------------------------------------------- loaders */

    private function loadCompany(CompanyUser $user): ?Company
    {
        if ($user->company_id === null) {
            return null;
        }

        return Company::query()
            ->with([
                'translations',
                'primaryCity:id,name',
                'contacts',
                'faqs',
                'media',
                'serviceAreas:id,name',
            ])
            ->find($user->company_id);
    }

    /* ---------------------------------------------- presenters */

    /**
     * @return array<string, mixed>
     */
    private function presentUser(CompanyUser $user): array
    {
        return [
            'id' => (int) $user->id,
            'first_name' => (string) $user->first_name,
            'last_name' => (string) $user->last_name,
            'full_name' => $user->fullName(),
            'email' => (string) $user->email,
            'company_id' => $user->company_id,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCompany(Company $company, string $locale): array
    {
        $translation = $company->translation($locale);

        return [
            'id' => (int) $company->id,
            'name' => $translation?->name ?? '',
            'permalink' => $translation?->permalink ?? null,
            'short_description' => $translation?->short_description ?? null,
            'about' => $translation?->about ?? null,
            'street' => $company->street,
            'postal_code' => $company->postal_code,
            'city' => $company->primaryCity?->name,
            'status' => (string) $company->status,
            'verified' => (bool) $company->verified,
            'is_top_rated' => (bool) $company->is_top_rated,
            'logo' => $company->logo,
            'cover' => $company->cover,
            'founded_year' => $company->founded_year,
            'employee_count' => $company->employee_count,
            'portal_url' => "/company-portal/{$company->id}"
                .($translation?->permalink !== null ? "/{$translation->permalink}" : ''),
            'admin_edit_url' => null, // reserved — partner never gets admin edit
        ];
    }

    /**
     * Compute how "done" the profile is as a % + which fields are
     * still missing. Missing-field labels are localized so the Vue
     * side can just render them as-is.
     *
     * @return array<string, mixed>
     */
    private function presentCompleteness(Company $company, string $locale): array
    {
        $translation = $company->translation($locale);
        $t = $this->completenessLabels($locale);

        $checks = [
            'name' => [(string) ($translation?->name ?? '') !== '', $t['name']],
            'short_description' => [(string) ($translation?->short_description ?? '') !== '', $t['short_description']],
            'about' => [mb_strlen((string) ($translation?->about ?? '')) > 40, $t['about']],
            'address' => [
                ($company->street !== null && $company->street !== '')
                    && ($company->postal_code !== null && $company->postal_code !== ''),
                $t['address'],
            ],
            'logo' => [$company->logo !== null && $company->logo !== '', $t['logo']],
            'cover' => [$company->cover !== null && $company->cover !== '', $t['cover']],
            'founded' => [$company->founded_year !== null, $t['founded']],
            'employees' => [$company->employee_count !== null, $t['employees']],
            'contacts' => [$company->contacts->isNotEmpty(), $t['contacts']],
            'gallery' => [$company->media->count() >= 3, $t['gallery']],
            'faqs' => [$company->faqs->count() >= 1, $t['faqs']],
            'service_areas' => [$company->serviceAreas->count() >= 1, $t['service_areas']],
        ];

        $done = 0;
        $missing = [];
        foreach ($checks as $slug => [$isDone, $label]) {
            if ($isDone) {
                $done++;
            } else {
                $missing[] = ['slug' => $slug, 'label' => $label];
            }
        }
        $total = count($checks);
        $pct = $total > 0 ? (int) round(100 * $done / $total) : 0;

        return [
            'percent' => $pct,
            'done' => $done,
            'total' => $total,
            'missing' => $missing,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function completenessLabels(string $locale): array
    {
        if ($locale === 'de') {
            return [
                'name' => 'Firmenname',
                'short_description' => 'Kurzbeschreibung',
                'about' => 'Ausführliche Beschreibung (mind. 40 Zeichen)',
                'address' => 'Straße und PLZ',
                'logo' => 'Logo hochladen',
                'cover' => 'Titelbild hochladen',
                'founded' => 'Gründungsjahr',
                'employees' => 'Mitarbeiterzahl',
                'contacts' => 'Mindestens ein Kontakt',
                'gallery' => 'Mindestens 3 Galeriebilder',
                'faqs' => 'Mindestens eine FAQ',
                'service_areas' => 'Mindestens ein Einsatzgebiet',
            ];
        }

        return [
            'name' => 'Company name',
            'short_description' => 'Short description',
            'about' => 'Detailed description (min. 40 chars)',
            'address' => 'Street and postal code',
            'logo' => 'Upload a logo',
            'cover' => 'Upload a cover image',
            'founded' => 'Year founded',
            'employees' => 'Number of employees',
            'contacts' => 'At least one contact',
            'gallery' => 'At least 3 gallery photos',
            'faqs' => 'At least one FAQ',
            'service_areas' => 'At least one service area',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentStats(Company $company): array
    {
        $unrepliedReviews = CompanyReview::query()
            ->where('company_id', $company->id)
            ->where('status', 'published')
            ->whereNull('reply_body')
            ->count();

        return [
            'review_count' => (int) ($company->review_count ?? 0),
            'rating_avg' => (float) ($company->rating_avg ?? 0),
            'recommend_pct' => (int) ($company->recommend_pct ?? 0),
            'google_rating' => $company->google_rating !== null
                ? (float) $company->google_rating
                : null,
            'google_review_count' => (int) ($company->google_review_count ?? 0),
            'photo_count' => $company->media->count(),
            'faq_count' => $company->faqs->count(),
            'service_area_count' => $company->serviceAreas->count(),
            'unreplied_reviews' => $unrepliedReviews,
        ];
    }

    /**
     * The five most recent published reviews, shaped for a compact
     * card list on the dashboard.
     *
     * @return array<int, array<string, mixed>>
     */
    private function presentRecentReviews(Company $company): array
    {
        return CompanyReview::query()
            ->where('company_id', $company->id)
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(5)
            ->get(['id', 'company_id', 'author_name', 'author_initials', 'is_anonymous', 'rating', 'body', 'reply_body', 'replied_at', 'published_at'])
            ->map(fn (CompanyReview $r): array => [
                'id' => (int) $r->id,
                'author_name' => $r->is_anonymous
                    ? (string) ($r->author_initials ?: '—')
                    : (string) ($r->author_name ?: '—'),
                'rating' => (int) $r->rating,
                'body' => mb_strimwidth((string) $r->body, 0, 180, '…'),
                'has_reply' => $r->reply_body !== null && $r->reply_body !== '',
                'replied_at' => $r->replied_at?->toIso8601String(),
                'published_at' => $r->published_at?->toIso8601String(),
            ])
            ->all();
    }

    /**
     * Portal deep-links used by the quick-actions grid on the
     * dashboard. Every URL points at the public portal editor —
     * behind the scenes each opens the correct section modal.
     *
     * @return array<int, array<string, mixed>>
     */
    private function presentQuickActions(Company $company, string $locale): array
    {
        $portal = "/company-portal/{$company->id}";
        $de = $locale === 'de';

        return [
            [
                'slug' => 'profile',
                'title' => $de ? 'Firmenprofil bearbeiten' : 'Edit company profile',
                'description' => $de
                    ? 'Name, Kurzbeschreibung, Kontakte und Adresse pflegen.'
                    : 'Update name, short description, contacts and address.',
                'href' => $portal,
                'icon' => 'store',
            ],
            [
                'slug' => 'gallery',
                'title' => $de ? 'Fotos & Galerie' : 'Photos & gallery',
                'description' => $de
                    ? 'Bilder Ihrer Fahrzeuge, Ihres Teams und abgeschlossener Umzüge.'
                    : 'Photos of your vehicles, team and completed moves.',
                'href' => $portal.'#gallery',
                'icon' => 'image',
            ],
            [
                'slug' => 'faqs',
                'title' => $de ? 'Häufige Fragen' : 'Frequently asked questions',
                'description' => $de
                    ? 'Wiederkehrende Fragen von Kunden beantworten.'
                    : 'Answer questions customers ask again and again.',
                'href' => $portal.'#faqs',
                'icon' => 'help-circle',
            ],
            [
                'slug' => 'areas',
                'title' => $de ? 'Einsatzgebiete' : 'Service areas',
                'description' => $de
                    ? 'Berlin-Bezirke und umliegende Städte auswählen.'
                    : 'Pick Berlin districts and surrounding cities.',
                'href' => $portal.'#areas',
                'icon' => 'map-pin',
            ],
            [
                'slug' => 'reviews',
                'title' => $de ? 'Bewertungen ansehen' : 'View reviews',
                'description' => $de
                    ? 'Alle Kundenbewertungen einsehen und darauf antworten.'
                    : 'See all customer reviews and reply to them.',
                'href' => $portal.'#reviews',
                'icon' => 'star',
            ],
            [
                'slug' => 'branding',
                'title' => $de ? 'Logo & Titelbild' : 'Logo & cover image',
                'description' => $de
                    ? 'Erscheinungsbild Ihres Profils anpassen.'
                    : 'Set the look and feel of your listing.',
                'href' => $portal.'#branding',
                'icon' => 'palette',
            ],
        ];
    }
}
