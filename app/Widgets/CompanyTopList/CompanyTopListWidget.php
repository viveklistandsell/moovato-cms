<?php

declare(strict_types=1);

namespace App\Widgets\CompanyTopList;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Ranked list of real companies from the admin ("Top 10 Umzugsunternehmen in
 * Berlin"). Unlike the hand-typed CompanyDirectory widget, this one references
 * `companies` rows by id: the editor picks them from the dashboard and
 * PageController hydrates name, logo, rating and permalink at render time, so
 * the section always mirrors the current company data.
 *
 * `auto` mode skips the manual pick and ranks published companies by
 * top-rated flag, then rating, then review count.
 */
final class CompanyTopListWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'company_top_list';
    }

    public static function label(): string
    {
        return 'Company Top List';
    }

    public static function icon(): string
    {
        return 'Trophy';
    }

    public static function category(): string
    {
        return 'marketing';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array
    {
        return [
            'mode' => 'auto',
            'company_ids' => [],
            'limit' => 10,
            'only_verified' => false,
            'show_filters' => true,
            'show_sort' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'heading' => 'Top 10 Umzugsunternehmen in Berlin',
            'subheading' => 'Geprüfte Umzugsunternehmen aus Berlin und Umgebung – Bewertungen vergleichen und kostenlos Angebote einholen.',
            'badge_label' => 'Bestbewertetes Umzugsunternehmen',
            'reviews_label' => 'Bewertungen',
            'recommend_label' => 'Weiterempfehlung',
            'quote_label' => 'Angebot anfordern',
            'quote_url' => '#',
            'details_label' => 'Details',
            'empty_text' => 'Derzeit sind keine Umzugsunternehmen verfügbar.',
            'count_text' => '{count} Umzugsunternehmen gefunden',
            'filters_title' => 'Filter',
            'results_title' => 'Ergebnisse',
            'services_title' => 'Dienstleistungen',
            'rating_title' => 'Bewertung',
            'features_title' => 'Merkmale',
            'nearby_title' => 'In der Nähe',
            'nearby_label' => 'Zeige Unternehmen in der Nähe von {city}',
            'verified_label' => 'Verifiziert',
            'top_rated_label' => 'Top bewertet',
            'no_rating_label' => 'Keine Bewertungen',
            'reset_label' => 'Filter zurücksetzen',
            'no_results_text' => 'Keine Umzugsunternehmen entsprechen Ihren Filtern.',
            'sort_label' => 'Sortieren nach:',
            'sort_relevance_label' => 'Am relevantesten',
            'sort_rating_label' => 'Beste Bewertung',
            'sort_reviews_label' => 'Meiste Bewertungen',
            'sort_name_label' => 'Name A–Z',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'mode' => ['nullable', 'string', 'in:auto,manual'],
            'company_ids' => ['nullable', 'array', 'max:30'],
            'company_ids.*' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:30'],
            'only_verified' => ['nullable', 'boolean'],
            'show_filters' => ['nullable', 'boolean'],
            'show_sort' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string', 'max:200'],
            'subheading' => ['nullable', 'string', 'max:400'],
            'badge_label' => ['nullable', 'string', 'max:120'],
            'reviews_label' => ['nullable', 'string', 'max:60'],
            'recommend_label' => ['nullable', 'string', 'max:60'],
            'quote_label' => ['nullable', 'string', 'max:80'],
            'quote_url' => ['nullable', 'string', 'max:2000'],
            'details_label' => ['nullable', 'string', 'max:80'],
            'empty_text' => ['nullable', 'string', 'max:400'],
            'count_text' => ['nullable', 'string', 'max:200'],
            'filters_title' => ['nullable', 'string', 'max:80'],
            'results_title' => ['nullable', 'string', 'max:80'],
            'services_title' => ['nullable', 'string', 'max:80'],
            'rating_title' => ['nullable', 'string', 'max:80'],
            'features_title' => ['nullable', 'string', 'max:80'],
            'nearby_title' => ['nullable', 'string', 'max:80'],
            'nearby_label' => ['nullable', 'string', 'max:200'],
            'verified_label' => ['nullable', 'string', 'max:80'],
            'top_rated_label' => ['nullable', 'string', 'max:80'],
            'no_rating_label' => ['nullable', 'string', 'max:80'],
            'reset_label' => ['nullable', 'string', 'max:80'],
            'no_results_text' => ['nullable', 'string', 'max:400'],
            'sort_label' => ['nullable', 'string', 'max:80'],
            'sort_relevance_label' => ['nullable', 'string', 'max:80'],
            'sort_rating_label' => ['nullable', 'string', 'max:80'],
            'sort_reviews_label' => ['nullable', 'string', 'max:80'],
            'sort_name_label' => ['nullable', 'string', 'max:80'],
        ];
    }
}
